<?php

namespace Navicu\InfrastructureBundle\Resources\Services;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Esta función es usada para el manejo de servicios relacionados
 * con la seguridad del sistema.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class SecurityService
{
    /**
     * Esta propiedad es usada para contener el contenedor de servicios
     * 
     * @var \Container_Services
     */
    protected $container;

    /**
     * Esta propiedad es usada para el manejo del entityManager.
     * 
     * @var \Security.context
     */
    protected $em;

    /**
     * Metodo Constructor de php
     *
     * @param \Container_Services $container
     * @param \Security.context $sessionManager
     */
    public function __construct($container, $em)
    {
        $this->em = $em;
        $this->container = $container;
    }

	/**
     * Esta función es usada para login de un usuario por
     * parametros necesarios para el mismo.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @author Currently Working: Joel D. Requena P.
	 * 
     * @param Array $data
     * @param String $config
     */
    public function loginByData($data, $config)
    {
        $factory = $this->container->get('security.encoder_factory');
        $repositoryUser = $this->em->getRepository('NavicuInfrastructureBundle:User');

        $user = $repositoryUser->findByUserNameOrEmail($data["userName"]);

        if (!$user)
            return false;

        $encoder = $factory->getEncoder($user);

        //Se verifica si password corresponde
        //Si el password corresponde se inicia la sesión
        if ($encoder->isPasswordValid($user->getPassword(),$data["pass"],$user->getSalt())) {
            $token = new UsernamePasswordToken($user, null, $config, $user->getRoles());
            $this->container->get("security.context")->setToken($token); 

            return true;
        } else {
            return false;
        }
    }

	/**
     * Esta función es usada para login de un usuario de forma
     * directa por medio de su userName.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @author Currently Working: Joel D. Requena P.
	 * 
     * @param Array $data
     * @param String $config
     */
    public function loginDirect($data, $config)
    {
        $repositoryUser = $this->em->getRepository('NavicuInfrastructureBundle:User');

        $user = $repositoryUser->findByUserNameOrEmail($data["userName"]);

        if (!$user)
            return false;

        $token = new UsernamePasswordToken($user, null, $config, $user->getRoles());
        $this->container->get("security.context")->setToken($token);
        return true;

    }
}