<?php

namespace Navicu\InfrastructureBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;

/**
 * La siguiente clase registra en el log los eventos
 * relacionados con autentificaciones de usuarios (login)
 *
 * Class AuthenticationListener
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @package Navicu\InfrastructureBundle\EventListener
 * @version 07/12/2016
 */
class AuthenticationListener implements EventSubscriberInterface
{
    /**
     * Contenedor de Symfony
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * Constructor
     */
    public function __construct()
    {
        global $kernel;
        $this->container = $kernel->getContainer();
    }

    /**
     * getSubscribedEvents
     *
     * @return 	array
     */
    public static function getSubscribedEvents()
    {
        return array(
            AuthenticationEvents::AUTHENTICATION_FAILURE => 'onAuthenticationFailure',
            AuthenticationEvents::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
        );
    }

    /**
     * Este evento es llamado cuando un usuario intenta hacer login
     * pero los datos son incorrectos.
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param Request $request
     * @param AuthenticationException $exception
     *
     * @return Response The response to return, never null
     */
    public function onAuthenticationFailure(AuthenticationFailureEvent $event)
    {
        $logger = $this->container->get('logger');
        $logger->error('login.failure:'.$this->getMessage());
    }

    /**
     * El siguiente evento se activa cuando un usuario
     * ingresa exitosamente al sistema
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param InteractiveLoginEvent $event
     */
    public function onAuthenticationSuccess(AuthenticationEvent $event )
    {
        if ($event->getAuthenticationToken()->getUser() != 'anon.') {
            $logger = $this->container->get('logger');
            $logger->info('login.success:'.$this->getMessage());
        }
    }

    /**
     * La función retorna el mensaje
     * almacenado en los archivos de logs del sistema
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @return mixed
     */
    private function getMessage()
    {
        $message = [];
        $message['router'] = $this->container->get('request')->getUri();
        $message['form'] = $this->container->get('request')->request->all();
        unset($message['form']['_password']);
        if (isset($message['form']['_username']))
            $message['user'] = $this->getProfile($message['form']['_username']);

        return str_replace('\/','/',json_encode($message));
    }

    /**
     * La función retorna una información mas detallada
     * del usuario que intenta hacer el inicio de sesión
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $username
     * @return array
     */
    private function getProfile($username)
    {
        // Se busca el usuario por username o el correo
        $user = $entityManager = $this->container->get('doctrine.orm.entity_manager')
            ->createQueryBuilder()
            ->select('u')
            ->from('NavicuInfrastructureBundle:User','u')
            ->where('u.username = :username OR u.email  = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();

        $response = [];

        // Si existe el usuario se valida el role y perfile en el sistema
        if ($user) {
            $response['roles'] = json_encode($user->getRoles());

            if ($user->getClientProfile())
                $response['profile'] = 'client';
            else if ($user->getOwnerProfile()) {
                $response['profile'] = 'owner_profile';
                $response['property_name'] = $user->getOwnerProfile()
                    ->getProperties()[0]->getName();
            } else if ($user->getNvcProfile()) {
                $response['profile'] = 'nvc_profile';
            } else if ($user->getAavvProfile()) {
                $response['profile'] = 'aavv_profile';
                $response['aavv_name'] = $user->getAavvProfile()->getAavv()->getSocialReason();
            } else if ($user->getTempOwner()) {
                $response['profile'] = 'temp_owner_profile';
                $propertyForm = $user->getTempOwner()->getPropertyForm();
                $response['temp_property_name'] = isset($propertyForm['name']) ? json_encode($propertyForm['name']) : null;
            } else
                $response['profile'] = 'Not associate profile';
        } else
            $response = 'Not Found';

        return $response;
    }
}
