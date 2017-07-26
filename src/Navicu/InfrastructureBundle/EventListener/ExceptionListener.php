<?php

namespace Navicu\InfrastructureBundle\EventListener;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListener
{

    public function __construct(TwigEngine $templating)
    {
        $this->templating = $templating;
    }

    public function onKernelException(GetResponseForExceptionEvent $event, $templating)
    {
        return;
        global $kernel;

        // Si estamos en entorno de desarrollo, dejamos el manejo de la
        // excepción a Symfony.
        if ($kernel->getEnvironment() == "dev") {
            return;
        }

        // Solo nos interesan las excepciones que ocurran bajo un subdominio.
        $params = $event->getRequest()->attributes->get('_route_params');
        if ($params == NULL || !array_key_exists('subdomain', $params)) {
            return;
        }

        // Creamos la respuesta que devolveremos, que será la del error 500
        // específica para AAVV.
        $response = new Response();
        $response->setContent($this->templating->render(
            'NavicuInfrastructureBundle:Errors/AAVV:500.html.twig'
        ));

        // Devolvemos la respuesta.
        $event->setResponse($response);
    }

}
