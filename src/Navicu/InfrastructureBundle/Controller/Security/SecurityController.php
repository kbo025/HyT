<?php

namespace Navicu\InfrastructureBundle\Controller\Security;

use Navicu\Core\Domain\Adapter\CoreSession;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Navicu\Core\Application\UseCases\Ascribere\CreateTempOwner\CreateTempOwnerCommand;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\JsonResponse;
use Navicu\Core\Application\UseCases\ClientInfoSession\ClientInfoSessionCommand;
use Navicu\Core\Application\UseCases\Reservation\setClientProfile\setClientProfileCommand;

/**
 * {@inheritDoc}
 */
class SecurityController extends Controller
{
    public function loginAction(Request $request)
    {
        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();
        if (class_exists('\Symfony\Component\Security\Core\Security')) {
            $authErrorKey = Security::AUTHENTICATION_ERROR;
            $lastUsernameKey = Security::LAST_USERNAME;
        } else {
            // BC for SF < 2.6
            $authErrorKey = SecurityContextInterface::AUTHENTICATION_ERROR;
            $lastUsernameKey = SecurityContextInterface::LAST_USERNAME;
        }
        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }
        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }
        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);
        if ($this->has('security.csrf.token_manager')) {
            $csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();
        } else {
            // BC for SF < 2.4
            $csrfToken = $this->has('form.csrf_provider')
                ? $this->get('form.csrf_provider')->generateCsrfToken('authenticate')
                : null;
        }
        return $this->renderLogin(array(
            'last_username' => $lastUsername,
            'error' => $error,
            'csrf_token' => $csrfToken,
        ));
    }

    public function checkAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }
    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }

    /**
     * La función carga la vista de login o hace el redireccionamiento
     * dependiendo el usuario actual
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param array $data
     * @version 09/10/2015
     */
    public function renderLogin(array $data)
    {
        $requestAttributes = $this->container->get('request')->attributes;
        $route = $requestAttributes->get('_route');
        $security = $this->container->get('security.context');



        if ('extranet_login' == $route) {

            if ($security->isGranted('ROLE_TEMPOWNER')) {
                $slug = $security->getToken()->getUser()->getUserName();
                return $this->redirect(
                    $this->generateUrl(
                        'navicu_register',
                        array('slug' => $slug)
                    )
                );
            } else if ($security->isGranted('IS_AUTHENTICATED_REMEMBERED'))
                return $this->redirectForRole($security);

            $form = $this->getFormRegisterOwner();
            $data['form'] = $form->createView();
            $data['form2'] = $form->createView();
            $template = sprintf('NavicuInfrastructureBundle:Security:loginExtranet.html.twig');

        } else if ('navicu_web_login' == $route) {

            $template = sprintf('NavicuInfrastructureBundle:Security:login.html.twig');

        } else if ('navicu_admin_login' == $requestAttributes->get('_route')) {

            $redirectUrl = $this->redirectAdmin($security);

            if ($redirectUrl) {
                return $this->redirect($redirectUrl);
            } else if ($security->isGranted('IS_AUTHENTICATED_REMEMBERED'))  {
                return $this->redirectForRole($security);
            }

            $template = sprintf('NavicuInfrastructureBundle:Admin:login.html.twig');

        } else if ('navicu_extranet_security_login' == $route) {

            if ($security->isGranted('ROLE_EXTRANET_USER')) {
                $property = CoreSession::getUser()->getOwnerProfile()->getProperties()[0];
                $slug = $property ? $property->getSlug() : null;

                if ($slug)
                    return $this->redirect($this->generateUrl('navicu_extranet_homepage',['slug'=> $slug]));
                else
                    return $this->redirect($this->generateUrl('navicu_error_401'));
            }
            else if ($security->isGranted('IS_AUTHENTICATED_REMEMBERED'))
                return $this->redirectForRole($security);

            $template = sprintf('NavicuInfrastructureBundle:Extranet\Security:login.html.twig');

        } else if ('nvc_web_service_login' == $route) {
            
            if ($security->isGranted('ROLE_EXTRANET_USER')) {
                return $this->redirect($this->generateUrl('navicu_render_request_token'));
            } else if ($security->isGranted('IS_AUTHENTICATED_REMEMBERED'))
                return $this->redirectForRole($security);

            $template = sprintf('NavicuInfrastructureBundle:WebService\Security:login.html.twig');

        } else if ('navicu_aavv_login' == $route) {

            if ($security->isGranted('ROLE_AAVV')) {
                $slugAavv = $security->getToken()->getUser()->getAavvProfile()->getAavv()->getSlug();
                $aavv = $this->container->get('doctrine.orm.entity_manager')
                    ->getRepository('NavicuDomain:AAVV')->findOneBy([
                        'slug' => $slugAavv,
                    ]);//CoreSession::getUser()->getAavvProfile()->getAavv();
                $urlLogo = null;

                if (!$aavv)
                    $customize = json_encode([
                        'activeButton' => '#62259d',
                        'buttonPrimary' => '#b42371',
                        'footer' => '#2e174b',
                        'icon' => '#391860',
                        'navbarMenu'=>'#391860',
                        'navbarPrimary' => '#783cbd',
                        'text' => '#808080',
                        'title' => '#391860',
                    ]);
                else {
                    $customize = json_encode($aavv->getCustomize());
                    $logo = $this->container->get('doctrine.orm.entity_manager')
                        ->getRepository('NavicuDomain:AAVVDocument')->findOneBy([
                            'aavv' => $aavv,
                            'type' => 'LOGO'
                        ]);
                    $urlLogo = $logo ? $logo->getDocument()->getFileName() : null;
                }

                $this->container->get('session')
                    ->set('customize',$customize);
                $this->container->get('session')
                    ->set('urlLogo',$urlLogo);

                if($aavv->getStatusAgency() == 2) {
                    $subdomain = $security->getToken()->getUser()->getSubdomain()->getSlug();

                    return $this->redirect(
                        $this->generateUrl(
                            'navicu_aavv_home',
                            array('subdomain' => $subdomain)
                        )
                    );
                } else {
                    return $this->redirect(
                        $this->generateUrl(
                            'aavv_register',
                            array('step' => 'company')
                        )
                    );
                }
            } else if ($security->isGranted('IS_AUTHENTICATED_REMEMBERED'))
                return $this->redirectForRole($security);

            /*$form = $this->getFormRegisterOwner();
            $data['form'] = $form->createView();
            $data['form2'] = $form->createView();*/

            $template = sprintf('NavicuInfrastructureBundle:AAVV\landing:login.html.twig');

        } else {
            $template = sprintf('FOSUserBundle:Security:login.html.twig');
        }

        return $this->container->get('templating')->renderResponse($template, $data);
    }

    /**
     * La siguiente función redirecciona al usuario segun su sesión
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $security
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @version 05/08/2015
     */
    private function redirectForRole($security)
    {
        if ($security->isGranted('ROLE_TEMPOWNER')) {
            $slug = $security->getToken()->getUser()->getUserName();

            return $this->redirect(
                $this->generateUrl(
                    'navicu_register',
                    array('slug' => $slug)
                )
            );
        } else if ($security->isGranted('ROLE_AAVV')) {
            $aavv = $security->getToken()->getUser()->getAavvProfile()->getAavv();
            if($aavv->getStatusAgency() == 2) {
                return $this->redirect('aavv_home');
            } else {
                return $this->redirect(
                    $this->generateUrl(
                        'aavv_register',
                        array('step' => 'company')
                    )
                );
            }

        } else if ($security->isGranted('ROLE_ADMIN')){
            return $this->redirect('navicu_admin_login');
        } else if ($security->isGranted('ROLE_EXTRANET_ADMIN')){
            return $this->redirect($this->generateUrl('navicu_extranet_security_login'));
        } else {
            return $this->redirect($this->generateUrl('navicu_homepage_temporal'));
        }
    }

    /**
     * La siguiente función se encarga de crear el formulario de registro
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
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
     * Esta función es usada para login de un usuario por
     * medio de peticion asincrona.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param Request $request
     * @param String $config
     */
    public function apiLoginAction(Request $request, $config)
    {
        if ($request->isXmlHttpRequest()) {

            $data = json_decode($request->getContent(), true);

            $response["response"] = $this->get('SecurityService')->loginByData($data, $config);

            if ($response["response"]) {

                $repositoryUser = $this->getDoctrine()->getRepository('NavicuInfrastructureBundle:User');

                // Si es un usuario de tipo Cliente envia datos adicionales.
                if ($config == "navicu_web") {
                    $user = $this->get('Security.context')->getToken()->getUser();

                    $includeSession = new ClientInfoSessionCommand(["user"=>$user]);
                    $this->get('CommandBus')->execute($includeSession);

                    $response["profile"] = $user->getClientProfile()->toArray();
                }
            }

            return new JsonResponse(
                $response,
                $response["response"] ? 200 : 400
            );

        } else {
            return new Response(
                'Not Found',
                404
            );
        }
    }

    /**
     * Esta función es usada para login de un usuario por
     * medio de peticion sincrona sin redireccionamiento.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param Request $request
     * @param String $config
     */
    public function webLoginAction(Request $request, $config)
    {
        $data["userName"] =  $request->request->get('_username');
        $data["pass"] =  $request->request->get('_password');

        $response["response"] = $this->get('SecurityService')->loginByData($data, $config);

        if ($response["response"]) {

            $repositoryUser = $this->getDoctrine()->getRepository('NavicuInfrastructureBundle:User');

            // Si es un usuario de tipo Cliente envia datos adicionales.
            if ($config == "navicu_web") {
                $user = $this->get('Security.context')->getToken()->getUser();

                $includeSession = new ClientInfoSessionCommand(["user"=>$user]);
                $this->get('CommandBus')->execute($includeSession);

                $response["profile"] = $user->getClientProfile()->toArray();
            }
        } else {
            $this->addFlash('fosUserError', 'true');
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Esta función es usada para no redireccionar al usuario
     * luego de que se desloguea.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param Request $request
     * @param String $config
     */
    public function noRedirectAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(true,200);
        }

        $lastRouting = $request->headers->get('referer');

        //Se verifica si se encentra en el modulo de cliente
        //Se debe redireccionar al homepage, al momento de hacer logout
        if (strpos(
            $lastRouting,$this->getRequest()->getHost().$this->getRequest()->getBaseUrl().'/client'))
            return $this->redirect($this->generateUrl('navicu_homepage_temporal'));
        else
            return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Esta función es usada para login de un usuario por
     * medio de peticion asincrona.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param Request $request
     * @param String $config
     */
    public function clientRegisterAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $data = json_decode($request->getContent(), true);

            $command = new setClientProfileCommand($data);
            $response = $this->get('CommandBus')->execute($command);

            if ($response->getStatusCode() == 201) {
                $this->get('SecurityService')->loginDirect(['userName'=>$data['email']], 'navicu_web');
                $user = $this->get('Security.context')->getToken()->getUser();

                $includeSession = new ClientInfoSessionCommand(["user"=>$user]);
                $this->get('CommandBus')->execute($includeSession);
            }

            return new JsonResponse($response->getData(), $response->getStatusCode());
        }
    }

    public function redirectAdmin($security)
    {
        $user = $security->getToken()->getUser();
        if ($security->isGranted('ROLE_ADMIN'))
            return $this->generateUrl('navicu_admin_homepage_affiliate_properties');
        else if ($security->isGranted('ROLE_SALES_EXEC'))
            return $this->generateUrl('navicu_admin_homepage_properties_without_price');
        else if ($security->isGranted('ROLE_DIR_FINANCIAL'))
            return $this->generateUrl('navicu_admin_property_list_reservation_template',['reservationStatus'=> 4]);
        else if (!is_string($user) and $user->getNvcProfile())
            return $this->generateUrl('navicu_admin_homepage');
        else
            false;
    }
}
