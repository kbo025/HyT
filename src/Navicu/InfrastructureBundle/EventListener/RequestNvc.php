<?php
namespace Navicu\InfrastructureBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Adapter\CoreUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Navicu\Core\Application\UseCases\Web\ForeignExchange\getUserLocation\getUserLocationCommand;
use Navicu\Core\Application\UseCases\Web\ForeignExchange\SetAlphaCurrency\SetAlphaCurrencyCommand;

/**
 * Clase RequestNvc
 *
 * Se define una clase y una serie de funciones necesarios para el manejo de
 * los eventos relacionado con las peticiones request.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
 */
class RequestNvc
{
    /**
     * Esta propiedad es usada para contener el contenedor de servicios
     *
     * @var \Container_Services
     */
    protected $container;

    /**
     * Esta propiedad es usada para la relacion con los repositorios
     * @var EntityManager
     */
    protected $em;
    protected $request;

    /**
     * Metodo Constructor de php
     *
     * @param \Container_Services $container
     * @param EntityManager $em
     * @param \request|RequestStack $request pila del request
     */
    public function __construct($container, EntityManager $em, RequestStack $request)
    {
        $this->container = $container;
        $this->em = $em;
        $this->request = $request;
    }

    /**
     * Esta función es usada para incluir dentro de la session lo
     * ciertos parametros de negocio.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $session = $this->container->get('session');

        if (!array_key_exists("userLocation", $session->all())) {
            $ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] :$_SERVER['REMOTE_ADDR'];
            $command = new getUserLocationCommand(["ip"=>$ip]);
            $response = $this->container->get('CommandBus')->execute($command);

            $data = $response->getData();
            $session->set('userLocation', $data["location"] == "VEN" ? "VEN":null);
            $session->set('symbolCurrency', $data["location"] == "VEN" ? "Bs" : $data['currency']);
            $session->set('alphaCurrency', $data["currency"]);
            $event->getRequest()->cookies->set("alphaCurrency", $data['currency']);
        }
        /*else {
            /* Si ya esta loggeado el usuario solo se hace el cambio de la moneda almacenada en session,
            * si la cookie y la session son distintas
            /
            $cookie = $event->getRequest()->cookies->get('alphaCurrency');
            $actualSession = $session->get("alphaCurrency");
            if (strcmp($cookie, $actualSession) != 0) {
                $data['alphaCurrency'] = $cookie;

                $command = new SetAlphaCurrencyCommand($data);
                $this->container->get('CommandBus')->execute($command);
            }
        }*/
    }

    public function onKernelSubDomainRequest(GetResponseEvent $event)
    {
        $subdomain = $event->getRequest()->get('subdomain');  //->getRequest();
        if ($subdomain and $subdomain != 'www') {

            $rpSubdomain = $this->em->getRepository('NavicuInfrastructureBundle:Subdomain');
            if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {

                $user = CoreSession::getUser();

                if (!$rpSubdomain->findBySlugUser($subdomain, $user) AND
                    !$this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
                    $router = $this->container->get('router');
                    $url = $router->generate('navicu_error_404');
                    $response = new RedirectResponse($url);
                    $event->setResponse($response);
                }
            } else {
                if (!$rpSubdomain->findOneBy(['slug' => $subdomain])) {
                    $router = $this->container->get('router');
                    $url = $router->generate('navicu_error_404');
                    $response = new RedirectResponse($url);
                    $event->setResponse($response);
                }
            }
        }
    }
}
