<?php

namespace Navicu\InfrastructureBundle\Resources\Services;
use Navicu\Core\Domain\Adapter\CoreSession;

/**
 *	Clase encargada de procesar funcionalidad respecto a la sesión de los usuarios
 *
 *	@author Freddy Contreras <freddycontreras3@gmail.com>
 *	@author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 *	@version 18/08/2015
 */
class SessionService
{
    /**
     * Esta propiedad es usada para contener el contenedor de servicios
     * 
     * @var \Container_Services
     */
    protected $container;

    /**
     * Esta propiedad es usada para el manejo de security.context.
     * 
     * @var \Security.context
     */
    protected $sessionManager;

    /**
     * Metodo Constructor de php
     *
     * @param \Container_Services $container
     * @param \Security.context $sessionManager
     */
    public function __construct($container, $sessionManager)
    {
        $this->sessionManager = $sessionManager;
        $this->container = $container;
    }

    /**
     * La siguien función verifica si la sesión ha expirado
     *
     * @param string $role
     * @return boolean
     */
    public function isLoggedIn($role = 'IS_AUTHENTICATED_ANONYMOUSLY')
    {
        $response = true;
        $securityContext = $this->container->get('security.context');

        if (!$securityContext->isGranted('IS_AUTHENTICATED_FULLY') or
            !$securityContext->isGranted($role)) {
            $response = false;
        }

        return $response;
    }

    /**
     * Esta función es usada para enviar desde la session del usuario un objeto
     * OwnerProfile. para el uso de la session de extranet.
     * 
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     * 
     * @param String $slug
     * @return Boolean
     */
    public function userOwnerSession()
    {
        $session = $this->sessionManager->getToken()->getUser();
        if (is_object($session) and !is_null($session->getOwnerProfile()))
            return $session->getOwnerProfile();
        else if ($session->hasRole('ROLE_ADMIN') or
            $session->hasRole('ROLE_DIR_COMMERCIAL') or
            $session->hasRole('ROLE_SALES_EXEC') or
            $session->hasRole('ROLE_TELEMARKETING'))
            return $session;
        return null;
    }

    public function getUserSession()
    {
        $session = $this->sessionManager->getToken()->getUser();
        if (is_object($session))
            return $session;
        return null;
    }

    /**
     * Esta función es usada para enviar desde la session del usuario un objeto
     * OwnerProfile. para el uso de la session de extranet.
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Gabriel Camacho
     *
     * @internal param String $slug
     * @return Boolean
     */
    public function userClientSession($email = null)
    {
        $session = $this->sessionManager->getToken()->getUser();
        if (is_object($session) and !is_null($session->getClientProfile())) {
            if (isset($email))
                return $session->getClientProfile()->getEmail() == $email ?
                    $session->getClientProfile() :
                    null;
            else
                return $session->getClientProfile();
        }
        return null;
    }

    /**
     * Esta función es usada para acceder dentro de la session y
     * guardar el lenguaje por preferencia del usuario.
     * 
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     * 
     * @param null 
     * @return Array
     */
    public function setLanguage($language)
    {

        switch($language){
            case "es":
                $language = "es_ES";
                break;
            case "en":
                $language = "en_US";
                break;
            default:
                return false;
                break;
        }
        
        $this->container->get('session')->set('_locale', $language);
        return true;
        
    }

    /**
     * esta funcion guarda en session una variable con true para ser usuada si el usuario AAVV en registro hizo click en
     * el boton de finalizar el registro
     *
     * @author Gabriel Camacho <gcamacho@navicu.com>
     * @author Currently Working: Gabriel Camacho
     *
     * @return true
     */
    public function setFinishRegistrationAAVV()
    {
        $this->container->get('session')->set('_finishRegistrationAAVV', true);
        return true;

    }

    /**
     * indica si el usuario AAVV hizo click en finalizar registro
     *
     * @author Gabriel Camacho <gcamacho@navicu.com>
     * @author Currently Working: Gabriel Camacho
     *
     * @return true
     */
    public function getFinishRegistrationAAVV()
    {
        return iseet($this->container->get('session')->get('_finishRegistrationAAVV')) ?
            $this->container->get('session')->get('_finishRegistrationAAVV') :
            false;
    }
}