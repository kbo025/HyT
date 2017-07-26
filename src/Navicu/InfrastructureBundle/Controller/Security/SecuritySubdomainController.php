<?php

namespace Navicu\InfrastructureBundle\Controller\Security;

use Navicu\Core\Domain\Adapter\CoreUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Navicu\Core\Domain\Adapter\CoreSession;

/**
 * {@inheritDoc}
 */
class SecuritySubdomainController extends Controller
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

        if ($route == 'navicu_subdomain_login') {

             if ($security->isGranted('ROLE_AAVV')) {

                 if (!$security->isGranted('ROLE_ADMIN'))
                    $aavv = CoreSession::getUser()->getAavvProfile()->getAavv();
                 else
                     $aavv = null;

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

                 return $this->redirect(
                    $this->generateUrl(
                        'navicu_subdomain_homepage',[
                            'subdomain' => $this->container->get('request')->get('subdomain')
                        ]
                    )
                );
            } else if ($security->isGranted('IS_AUTHENTICATED_REMEMBERED'))
                return $this->redirectForRole($security);

            $template = sprintf('NavicuInfrastructureBundle:AAVV\landing:loginSubdomain.html.twig');
        } else {
            $template = sprintf('NavicuInfrastructureBundle:AAVV\landing:login.html.twig');
        }

        return $this->container->get('templating')->renderResponse($template, $data);
    }

    /**
     * La siguiente función valida si un usuario (administrador) tiene
     * acceso al modulo de agencia de viaje
     *
     * Esta función debe ser llamada desde paixarei-aspra
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $username
     * @param $passwordRequest
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function accessAdminAction($username)
    {
        $user = $this->get('doctrine.orm.entity_manager')->getRepository('NavicuInfrastructureBundle:User')
            ->findByUserNameOrEmail($username);

        if ($user->hasRole('ROLE_ADMIN')) {
            $token = new UsernamePasswordToken($user, null, 'navicu_subdomain', $user->getRoles());
            $this->container->get("security.context")->setToken($token);

            return $this->redirect(
                $this->generateUrl(
                    'navicu_subdomain_homepage',[
                        'subdomain' => $this->container->get('request')->get('subdomain')
                    ]
                )
            );
        } else {
            return $this->redirect(
                $this->generateUrl(
                    'navicu_subdomain_login',[
                        'subdomain' => $this->container->get('request')->get('subdomain')
                    ]
                )
            );
        }
    }
}