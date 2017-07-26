<?php
namespace Navicu\InfrastructureBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;



/**
 * Clase MaintenanceListener
 *
 * La clase verifica si el sistema se encuentra en mantenimiento
 * y hace el respectivo redireccionamiento a la página de mantenimiento
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 */
class MaintenanceListener
{
    /**
     * Esta propiedad es usada para contener el contenedor de servicios
     *
     * @var \Container_Services
     */
    protected $container;

    /**
     * Metodo Constructor de php
     *
     * @param \Container_Services $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $maintenance = $this->container->hasParameter('maintenance') ? $this->container->getParameter('maintenance') : false;

        //$debug = in_array($this->container->get('kernel')->getEnvironment(), array('test', 'dev'));

        if ($maintenance) {
            $engine = $this->container->get('templating');
            $content = $engine->render('NavicuInfrastructureBundle::Web/maintenanceView.html.twig');
            $response = new Response($content, 503);
            $protocol = "HTTP/1.1" == $_SERVER["SERVER_PROTOCOL"] ? "HTTP/1.1" : "HTTP/1.0";
            $response->headers->set("Retry-After", "3600");
            $response->headers->set($protocol."503 Service Unavailable", true, 503);
            $response->headers->set("status", "503");
            $event->setResponse($response);
            $event->stopPropagation();
        }
    }
}