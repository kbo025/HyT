<?php
namespace Navicu\InfrastructureBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Clase CorsListener
 *
 * Se define una clase y una serie de funciones necesarios para
 * de peticiones de tipo REST API.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
 */
class CorsListener
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
    public function __construct($container)
    {
        $this->container = $container;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $domain = $this->container->getParameter('blog_domain');

        $httpOrigin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : null;
        if ($httpOrigin == $domain) {
            $responseHeaders = $event->getResponse();

            $responseHeaders->headers->set('Access-Control-Allow-Credentials', 'false');
            $responseHeaders->headers->set('Access-Control-Allow-Methods', 'POST, OPTIONS');
            $responseHeaders->headers->set('Access-Control-Allow-Headers', 'X-Requested-With, Origin, Content-Type, Accept, Authorization');
            $responseHeaders->headers->set('Access-Control-Max-Age', 3600);
            $responseHeaders->headers->set('Access-Control-Allow-Origin', $domain);
        }
    }
}