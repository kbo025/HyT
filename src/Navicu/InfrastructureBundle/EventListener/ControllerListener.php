<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 13/10/16
 * Time: 04:25 PM
 */

namespace Navicu\InfrastructureBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Adapter\CoreTranslator;
use Navicu\Core\Domain\Model\Entity\AAVVTopDestination;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class ControllerListener
{
    private $container;
    private $em;
    private $request;

    public function __construct($container, EntityManager $em, RequestStack $request)
    {
        $this->container = $container;
        $this->em = $em;
        $this->request = $request;
    }

    /**
     * Funcion encargada de registrar las busquedas que se estan realizando desde la aavv
     * y guardarla en la tabla aavvTopDestination y posiblemente mas adelante las de navicu
     * para mostrar los destinos mas buscados
     *
     * @param FilterControllerEvent $event
     * @version 17/10/2016
     * @author Isabel Nieto <isabelcnd@gmail.com>
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $location = $this->em->getRepository('NavicuDomain:Location');
        $controller = $event->getController();

        $array_attributes_request = $this->request->getCurrentRequest()->attributes->get("_route_params");

        if ($controller[0] instanceof \Navicu\InfrastructureBundle\Controller\AAVV\SearchController) {
            // Agregamos los otros metodos por los que se realizan las busquedas
            if ($controller[1] === 'getListPropertiesAction') {
                foreach ($array_attributes_request as $att => $value) {
                    if (strcmp($att, "type") == 0)
                        $type =  CoreTranslator::getTranslator($value,'location');
                    else if (strcmp($att, "slug") == 0)
                        $slug = $value;
                    else if (strcmp($att, "countryCode") == 0)
                        $country = $value;
                }

                $aavv = (strcmp(gettype(CoreSession::getUser()), "string") == 0) ?
                    null :
                    (method_exists(CoreSession::getUser()->getAavvProfile(), "getAavv")) ?
                        CoreSession::getUser()->getAavvProfile()->getAavv() :
                        null;
                // Si se esta realizando una busqueda desde la aavv
                if (!is_null($aavv)) {
                    $destination = $location->findOneByCountrySlugType(
                        $country,
                        $slug,
                        $type
                    );
                    // Si se encontro un destino lo agregamos a la lista de top destinos de esa aavv
                    if (count($destination) > 0) {
                        $topDestinationRepo = $this->em->getRepository('NavicuDomain:AAVVTopDestination');
                        $aavvRepo = $this->em->getRepository('NavicuDomain:AAVV');
                        $topDestinationFound = $topDestinationRepo->findOneByArray(["location" => $destination->getId()]);
                        //Si se encontro un destino existente
                        if (count($topDestinationFound) > 0) {
                            $count = $topDestinationFound->getNumberVisits();
                            $topDestinationFound->setNumberVisits($count + 1);
                            $topDestinationRepo->persistObject($topDestinationFound);
                        } else { // Si es un destino nuevo que se tiene que agregar
                            $newTopDestination = new AAVVTopDestination();
                            $newTopDestination->setAavv($aavv);
                            $newTopDestination->setLocation($destination);
                            // Enlazamos hacia la aavv
                            $aavv->addTopDestination($newTopDestination);

                            // Guardamos la relacion generada
                            $topDestinationRepo->persistObject($newTopDestination);
                            $aavvRepo->persistObject($aavv);
                        }
                        $topDestinationRepo->flushObject();
                    }
                }
            }
            /*else ($controller[1] === 'listSearchAction') {
                $location = $this->em->getRepository('NavicuDomain:Location');

                $destination = $location->findOneByCountrySlugType(
                    $country,
                    $slug,
                    $type
                );
                dump($controller, $destination);
            }*/
        }
    }
}