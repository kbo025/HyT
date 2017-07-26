<?php

namespace Navicu\InfrastructureBundle\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Navicu\Core\Application\UseCases\CreateTempOwner\CreateTempOwnerCommand;

/**
 * {@inheritDoc}
 */
class ErrorsController extends Controller
{
    /**
     * Esta función es usada para redireccionar al error 404
     * 
     * @author Helen Mercatudo <hmercatudo@navicu.com>
     * @author Currently Working: Helen Mercatudo
     *
     */
    public function error404Action()
    {
        return $this->render(
            'NavicuInfrastructureBundle:Errors:404.html.twig'
        );
    }

    /**
     * Esta función es usada para redireccionar al error 401
     * 
     * @author Helen Mercatudo <hmercatudo@navicu.com>
     * @author Currently Working: Helen Mercatudo
     *
     */
    public function error401Action()
    {
        return $this->render(
            'NavicuInfrastructureBundle:Errors:401.html.twig'
        );
    }

    /**
     * Esta función es usada para redireccionar al error 500
     *
     * @author Helen Mercatudo <hmercatudo@navicu.com>
     * @author Currently Working: Helen Mercatudo
     *
     */
    public function error500Action()
    {
        if ($this->container->get('request')->get('extension') == "aavv") {
            return $this->render(
                'NavicuInfrastructureBundle:Errors/AAVV:500.html.twig'
            );
        }

        return $this->render(
            'NavicuInfrastructureBundle:Errors:500.html.twig'
        );
    }
}
