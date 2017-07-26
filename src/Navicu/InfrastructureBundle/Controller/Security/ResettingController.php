<?php
/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Navicu\InfrastructureBundle\Controller\Security;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Model\User;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Navicu\Core\Application\UseCases\Ascribere\CreateTempOwner\CreateTempOwnerCommand;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller managing the resetting of the password
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class ResettingController extends Controller
{
    /**
     * Request reset user password: show form
     */
    public function requestAction()
    {
        $requestAttributes = $this->container->get('request')->attributes;
        $route = $requestAttributes->get('_route');
        if ($route === 'navicu_extranet_resetting_register') {
            $form = $this->getFormRegisterOwner();
            return $this->render('NavicuInfrastructureBundle:Resetting:request.html.twig', array( 'slugTemp' => null, 'form2' => $form->createView()));
        } else if ($route === 'navicu_extranet_resetting'){
            return $this->render('NavicuInfrastructureBundle:Resetting:request.html.twig');
        } else if ($route === 'navicu_web_resetting') {
            return $this->render('NavicuInfrastructureBundle:Resetting:request.html.twig');
        } else if ($route === 'navicu_aavv_resetting') {
            return $this->render('NavicuInfrastructureBundle:Resetting:request.html.twig');
        }
        return $route;
    }
    /**
     * Request reset user password: submit form and send email
     */
    public function sendEmailAction(Request $request)
    {
        $requestAttributes = $this->container->get('request')->attributes;
        $route = $requestAttributes->get('_route');

        $username = $request->request->get('username');
        $form = $this->getFormRegisterOwner();
        /** @var $user UserInterface */
        $user = $this->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

        if (null === $user) {
            if ($route == 'navicu_extranet_resetting_register_send_email')
                return $this->render('NavicuInfrastructureBundle:Resetting:request.html.twig', array(
                    'invalid_username' => $username, 'form2' => $form->createView()
                ));
            else if ($route == 'navicu_extranet_resetting_send_email')
                return $this->render('NavicuInfrastructureBundle:Resetting:request.html.twig', array(
                    'invalid_username' => $username
                ));
            else if ($route == 'navicu_web_resetting_send_email')
                return $this->render('NavicuInfrastructureBundle:Resetting:request.html.twig', array(
                    'invalid_username' => $username
                ));
            else if ($route == 'navicu_aavv_resetting_send_email')
                return $this->render('NavicuInfrastructureBundle:Resetting:request.html.twig', array(
                    'invalid_username' => $username
                ));
        }

        if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            if ($route == 'navicu_extranet_resetting_register_send_email')
                return $this->render('NavicuInfrastructureBundle:Resetting:passwordAlreadyRequested.html.twig',
                    array( 'form2' => $form->createView()));
            else if ($route == 'navicu_extranet_resetting_send_email')
                return $this->render('NavicuInfrastructureBundle:Resetting:passwordAlreadyRequested.html.twig');
            else if ($route == 'navicu_web_resetting_send_email')
                return $this->render('NavicuInfrastructureBundle:Resetting:passwordAlreadyRequested.html.twig');
            else if ($route == 'navicu_aavv_resetting_send_email')
                return $this->render('NavicuInfrastructureBundle:Resetting:passwordAlreadyRequested.html.twig');
        }


        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
            $tokenGenerator = $this->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $this->sendConfirmationEmail($user, $route);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->get('fos_user.user_manager')->updateUser($user);

        if ($route == 'navicu_extranet_resetting_register_send_email')
            return new RedirectResponse($this->generateUrl('navicu_extranet_resetting_register_check_email',
                array('email' => $this->getObfuscatedEmail($user))
            ));
        else if ($route == 'navicu_extranet_resetting_send_email')
            return new RedirectResponse($this->generateUrl('navicu_extranet_resetting_check_email',
                array('email' => $this->getObfuscatedEmail($user))
            ));
        else if ($route == 'navicu_web_resetting_send_email')
            return new RedirectResponse($this->generateUrl('navicu_web_resetting_check_email',
                array('email' => $this->getObfuscatedEmail($user))
            ));
        else if ($route == 'navicu_aavv_resetting_send_email')
            return new RedirectResponse($this->generateUrl('navicu_aavv_resetting_check_email',
                array('email' => $this->getObfuscatedEmail($user), 'sent' => true, 'subdomain' => 'www')
            ));
    }

    /**
     * La función se encarga de enviar el correo de reinicio de contraseña
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param User $user
     * @param $route
     * @version 09/10/2015
     */
    public function sendConfirmationEmail($user, $route, $userName = null)
    {
        $emailService = $this->get('EmailService');
        $emailService->setConfigEmail('first_mailer');
        $emailService->setRecipients(array($user->getEmail()));
        $emailService->setSubject('Recuperar Contraseña - Navicu');

        if (!is_null($userName))
            $emailData['user'] = $userName;
        else
            $emailData['user'] = $user->getUsername();

        $emailService->setViewRender('NavicuInfrastructureBundle:Email:resettingPassword.html.twig');

        if ($user->hasRole('ROLE_TEMPOWNER')) {
            $emailData['confirmationUrl'] = $this->get('router')->generate('navicu_extranet_resetting_register_reset', array('token' => $user->getConfirmationToken()), true);
        } else if ($user->hasRole('ROLE_EXTRANET_ADMIN')) {
            $emailData['confirmationUrl'] = $this->get('router')->generate('navicu_extranet_resetting_reset', array('token' => $user->getConfirmationToken()), true);
        } else if ($user->hasRole('ROLE_WEB')) {
            $emailData['confirmationUrl'] = $this->get('router')->generate('navicu_web_resetting_reset', array('token' => $user->getConfirmationToken()), true);
        } else if ($user->hasRole('ROLE_AAVV')) {
            $emailData['confirmationUrl'] = $this->get('router')->generate(
                'navicu_aavv_resetting_reset',
                [
                    'token' => $user->getConfirmationToken(),
                    "subdomain"=>$this->container->get('request')->get('subdomain')
                ],
                true);
        }

        $emailService->setViewParameters($emailData);
        $emailService->setEmailSender('info@navicu.com');
        $emailService->sendEmail();
    }

    /**
     * Tell the user to check his email provider
     */
    public function checkEmailAction(Request $request)
    {
        $email = $request->query->get('email');
        if (empty($email)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse($this->generateUrl('navicu_extranet_resetting'));
        }

        $requestAttributes = $this->container->get('request')->attributes;
        $route = $requestAttributes->get('_route');

        if ($route === 'navicu_extranet_resetting_register_check_email') {
            $form = $this->getFormRegisterOwner();
            return $this->render('NavicuInfrastructureBundle:Resetting:checkEmail.html.twig', array(
                'email' => $email, 'form2' => $form->createView()
            ));
        } else if ($route === 'navicu_extranet_resetting_check_email') {
            return $this->render('NavicuInfrastructureBundle:Resetting:checkEmail.html.twig', array(
                'email' => $email
            ));
        } else if ($route === 'navicu_web_resetting_check_email') {
            return $this->render('NavicuInfrastructureBundle:Resetting:checkEmail.html.twig', array(
                'email' => $email
            ));
        } else if ($route === 'navicu_aavv_resetting_check_email') {
            return $this->render('NavicuInfrastructureBundle:Resetting:checkEmail.html.twig', array(
                'email' => $email
            ));
        }
    }
    /**
     * Reset user password
     */
    public function resetAction(Request $request, $token)
    {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.resetting.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->findUserByConfirmationToken($token);
        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_SUCCESS, $event);
            $userManager->updateUser($user);
            if (null === $response = $event->getResponse()) {
                if ($user->hasRole('ROLE_TEMPOWNER'))
                    $url = $this->generateUrl('navicu_extranet_resetting_register_update_password_success');
                else if ($user->hasRole('ROLE_EXTRANET_USER') or $user->hasRole('ROLE_EXTRANET_ADMIN'))
                    $url = $this->generateUrl('navicu_extranet_resetting_update_password_success');
                else if ($user->hasRole('ROLE_WEB'))
                    $url = $this->generateUrl('navicu_web_resetting_update_password_success');
                else if ($user->hasRole('ROLE_AAVV'))
                    $url = $this->generateUrl('navicu_aavv_resetting_update_password_success',array(
                        'subdomain' => 'www'
                    ));

                $response = new RedirectResponse($url);
            }
            $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_COMPLETED, new FilterUserResponseEvent($user, $request, $response));
            return $response;
        }

        $requestAttributes = $this->container->get('request')->attributes;
        $route = $requestAttributes->get('_route');

        if ($route == 'navicu_extranet_resetting_register_reset') {
            $form2 = $this->getFormRegisterOwner();
            return $this->render('NavicuInfrastructureBundle:Resetting:reset.html.twig', array(
                'token' => $token, 'form' => $form->createView(), 'form2' => $form2->createView()
            ));
        } else if ($route == 'navicu_extranet_resetting_reset') {
            return $this->render('NavicuInfrastructureBundle:Resetting:reset.html.twig', array(
                'token' => $token, 'form' => $form->createView()
            ));
        } else if ($route == 'navicu_web_resetting_reset') {
            return $this->render('NavicuInfrastructureBundle:Resetting:reset.html.twig', array(
                'token' => $token, 'form' => $form->createView()
            ));
        } else if ($route == 'navicu_aavv_resetting_reset') {
            return $this->render('NavicuInfrastructureBundle:Resetting:reset.html.twig', array(
                'token' => $token, 'form' => $form->createView()
            ));
        }
    }
    /**
     * Get the truncated email displayed when requesting the resetting.
     *
     * The default implementation only keeps the part following @ in the address.
     *
     * @param \FOS\UserBundle\Model\UserInterface $user
     *
     * @return string
     */
    protected function getObfuscatedEmail(UserInterface $user)
    {
        return $user->getEmail();
    }

    /**
     * La siguiente función retorn un formulario del registro registro hotelero
     *
     * @author Freddy Contreras <freddycontreras@gmail.com>
     * @version 05/08/2015
     * @return \Symfony\Component\Form\Form
     */
    private function getFormRegisterOwner()
    {
        $command = new CreateTempOwnerCommand();

        $translation = $this->get('translator')->trans('share.action.sign_up');

        $form = $this->createFormBuilder($command)
            ->setAction($this->generateUrl('navicu_register_owner'))
            ->add('username', 'text')
            ->add('email', 'email')
            ->add('password', 'password', array('invalid_message' => 'Passwords do not match'))
            ->add('save', 'submit', array('label' => $translation))
            ->getForm();

        return $form;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function passwordUpdateSuccessAction()
    {
        $requestAttributes = $this->container->get('request')->attributes;
        $route = $requestAttributes->get('_route');

        if ($route === 'navicu_extranet_resetting_register_update_password_success')
            return $this->render('NavicuInfrastructureBundle:Resetting:updatePasswordSuccess.html.twig');
        if ($route === 'navicu_extranet_resetting_update_password_success')
            return $this->render('NavicuInfrastructureBundle:Resetting:updatePasswordSuccess.html.twig');
        if ($route === 'navicu_web_resetting_update_password_success')
            return $this->render('NavicuInfrastructureBundle:Resetting:updatePasswordSuccess.html.twig');
        if ($route === 'navicu_aavv_resetting_update_password_success')
            return $this->render('NavicuInfrastructureBundle:Resetting:updatePasswordSuccess.html.twig');
    }


	/**
     * Esta función es usada para enviar un correo con el token
     * de restablecer la contraseña a un usuario.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @author Currently Working: Joel D. Requena P.
	 * 
     * @param Request $request
     */
    public function apiSendEmailAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $data = json_decode($request->getContent(), true);
            $username = $data['username'];
            $route = $data["type"];
            
            $locale = $this->get('session')->get('_locale');

            /** @var $user UserInterface */
            $user = $this->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

            if (!$user) {
                $response = $this->get('translator')->trans(
                    "share.message.resetting.invalid_username",
                    array(),
                    'messages',
                    $locale
                );

                return new JsonResponse(
                    ["response"=>$response],
                    400
                );
            }

            if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {

                $response = $this->get('translator')->trans(
                    "share.message.resetting.async.email_already_requested",
                    array(),
                    'messages',
                    $locale
                );

                return new JsonResponse(
                    ["response"=>$response],
                    400
                );
            }

            if (null === $user->getConfirmationToken()) {
                /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
                $tokenGenerator = $this->get('fos_user.util.token_generator');
                $user->setConfirmationToken($tokenGenerator->generateToken());
            }

            $clientProfile = $user->getClientProfile();
            if ($clientProfile)
                $this->sendConfirmationEmail($user, $route, $clientProfile->getFullName());
            else
                $this->sendConfirmationEmail($user, $route);

            $user->setPasswordRequestedAt(new \DateTime());
            $this->get('fos_user.user_manager')->updateUser($user);

            $response = $this->get('translator')->trans(
                    "share.message.check_email",
                    array(),
                    'messages',
                    $locale
                );

            return new JsonResponse(
                    ["response"=>$response],
                    200
                );
        }
    }
}