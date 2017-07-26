<?php
namespace Navicu\Core\Application\UseCases\Admin\PropertyCRUD\GetDataGalleries;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Model\Entity\Property;

/**
 * La siguiente clase implementa el handler del caso de uso "GetDataGalleries"
 * Donde de consultará los detalles de las galeria de imagenes de un establecimiento
 *
 * Class GetDetailsPropertyHandler
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 17/11/2015
 */
class GetDataGalleriesHandler implements Handler
{
    /**
     *   Instancia del repositoryFactory
     *   @var RepositoryFactory $rf
     */
    protected $rf;

    /**
     * Ejecuta las tareas solicitadas
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 17/11/2015
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle( Command $command, RepositoryFactoryInterface $rf )
    {
        $this->rf = $rf;
        $rpProperty = $this->rf->get('Property');
        $property = $rpProperty->findOneBy(array('slug' => $command->getSlug()));

        if ($property) {

            $response = array();
            $response['favorites'] = $this->getFavorite($property);
            $response['galleries'] = $this->getGalleries($property);
            $this->checkGalleries($property, $response['galleries']);

            return new ResponseCommandBus(200, 'Ok', json_encode($response));
        }

        return new ResponseCommandBus(404, 'No found');
    }

    /**
     * La siguiente función se encarga de obtener todas las imagenes
     * que son favoritas de un establecimiento
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param Property $property
     * @return array
     */
    private function getFavorite(Property $property)
    {
        $response = array();

        foreach ($property->getPropertyFavoriteImages() as $currentFavorite) {
            $auxFavorite = array();
            $auxFavorite['idFavorite'] = $currentFavorite->getId();
            $auxFavorite['path'] = $currentFavorite->getImage()->getFileName();
            $auxFavorite['orderGallery'] = $currentFavorite->getOrderGallery();

            $rpRoomImagesGallery = $this->rf->get('RoomImagesGallery');
            $roomImage = $rpRoomImagesGallery->findOneBySlugImage(
                $property->getSlug(),
                $currentFavorite->getImage()->getId()
            );

            if ($roomImage)
                $auxFavorite['nameGallery'] = $roomImage->getRoom()->getName();
            else {
                $rpPropertyImagesGallery = $this->rf->get('PropertyImagesGallery');
                $imageGallery = $rpPropertyImagesGallery->findOneBySlugImage(
                    $property->getSlug(),
                    $currentFavorite->getImage()->getId()
                );

                if ($imageGallery) {
                    $auxFavorite['nameGallery'] = $imageGallery->getPropertyGallery()
                        ->getType()->getTitle();
                }
            }

            array_push($response, $auxFavorite);
        }

        usort($response, function($a, $b) {
            return $a['orderGallery'] - $b['orderGallery'];
        });

        return $response;
    }

    /**
     * Retorna la información de las galerias
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param Property $property
     * @return array
     * @version 17/11/2015
     */
    private function getGalleries(Property $property)
    {
        $response = array();

        foreach ($property->getRooms() as $currentRoom) {
            $auxGalleryRoom = array();
            $auxGalleryRoom['idGallery'] = $currentRoom->getId();
            $auxGalleryRoom['typeGallery'] = 'rooms';
            $auxGalleryRoom['nameGallery'] = $currentRoom->getName();
            $auxGalleryRoom['features'] = array();
            
            foreach ($currentRoom->getFeatures() as $currentFeature) {
                
                if ($currentFeature->getFeature()->getTitle() == 'Habitación' and 
                    $currentFeature->getAmount() < 2) {
                    
                    array_push($auxGalleryRoom['features'], 'Detalle de la habitación');
                    array_push($auxGalleryRoom['features'], 'Detalle de servicios de la habitación');
                    array_push($auxGalleryRoom['features'],"Exterior habitación");
                    array_push($auxGalleryRoom['features'],"Tipos de camas");
                    array_push($auxGalleryRoom['features'],"Vistas de la habitación");

                } else if ($currentFeature->getAmount() < 2 )
                     
                    array_push($auxGalleryRoom['features'],
                        $currentFeature->getFeature()->getTitle());
                    
                else 
                    for ($i = 1; $i <= $currentFeature->getAmount(); $i++)
                        array_push(
                            $auxGalleryRoom['features'],
                            $currentFeature->getFeature()->getTitle()." ".$i
                        );

            }            

            $auxGalleryRoom['features'] = array_unique($auxGalleryRoom['features']);
            sort($auxGalleryRoom['features']);
            
            $auxGalleryRoom['loadedImages'] = array();

            foreach ($currentRoom->getImagesGallery() as $currentImage) {

                $auxImage = array();
                $auxImage['idImage'] = $currentImage->getImage()->getId();
                $auxImage['path'] = $currentImage->getImage()->getFileName();
                $auxImage['feature'] = $currentImage->getImage()->getName();
                $auxImage['orderGallery'] = $currentImage->getOrderGallery();
                array_push($auxGalleryRoom['loadedImages'], $auxImage);
            }

            usort($auxGalleryRoom['loadedImages'], function($a, $b) {
                return $a['orderGallery'] - $b['orderGallery'];
            });

            array_push($response, $auxGalleryRoom);
        }

        foreach ($property->getPropertyGallery() as $currentGallery) {
            $auxGallery = array();
            $auxGallery['idGallery'] = $currentGallery->getId();
            $auxGallery['typeGallery'] = 'otherGalleries';
            $auxGallery['nameGallery'] = $currentGallery->getType()->getTitle();
            $auxGallery['loadedImages'] = array();

            $auxGallery['features'] = $this->getFeatureService(
                $property,
                $currentGallery
            );

            sort($auxGallery['features']);

            foreach ($currentGallery->getImagesGallery() as $currentImage) {
                $auxImage = array();
                $auxImage['idImage'] = $currentImage->getImage()->getId();
                $auxImage['path'] = $currentImage->getImage()->getFileName();
                $auxImage['feature'] = $currentImage->getImage()->getName();
                $auxImage['orderGallery'] = $currentImage->getOrderGallery();
                array_push($auxGallery['loadedImages'], $auxImage);
            }

            usort($auxGallery['loadedImages'], function($a, $b) {
                return $a['orderGallery'] - $b['orderGallery'];
            });

            array_push($response, $auxGallery);
        }

        return $response;
    }

    /**
     * Retorna las galerías del establecimiento con sus respectivas caracteristicas
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param Property $property
     * @param $service
     * @return array
     * @version 18/11/2015
     */
    private function getFeatureService(Property $property, $gallery)
    {
        $response = array();
        $service = $gallery->getType()->getTitle();
        $rpPropertyService = $this->rf->get('PropertyService');
        $rpBar = $this->rf->get('Bar');
        $rpSalon = $this->rf->get('Salon');
        $rpRestaurant = $this->rf->get('Restaurant');

        $response = $this->getDefaultGallery($service);

        $services = $rpPropertyService->findByPropertyService($property->getSlug(), $service);

        if ($services) {
            foreach ($services as $currentService) {
                $parentService = null;
                if ($service == 'Deporte y recreación')
                    $parentService = $currentService->getType()->getParent();
                else if ($service == 'Zonas comunes' or $service == 'Spa')
                    $parentService = $currentService->getType();

                if (!is_null($parentService)) {
                    $parentServiceTitle = $parentService->getTitle();
                    if (!in_array($parentServiceTitle, $response))
                        array_push($response, $parentServiceTitle);
                }
            }
        } else if ($service == 'Bar') {

                $bars = $rpBar->findBySlug($property->getSlug());
                foreach ($bars as $currentBar)
                    array_push($response, $currentBar->getName());
                array_push($response,'Barra de bar');

        } else if ($service == 'Salones') {

            $salon = $rpSalon->findBySlug($property->getSlug());
            foreach ($salon as $currentSalon)
                array_push($response, $currentSalon->getName());

        } else if ($service == 'Restaurante') {

            $restaurants = $rpRestaurant->findBySlug($property->getSlug());
            foreach ($restaurants as $currentRest)
                array_push($response, $currentRest->getName());

        }

        return $response;
    }

    /**
     * La función retorna algunos valores predeterminado según la galería
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $service
     * @return array
     * @version 18/11/215
     */
    private function getDefaultGallery($service)
    {
        $response = array();
        switch ($service) {
            case 'Zonas comunes':
                $response = array(
                    'Baños',
                    'Caminería exterior',
                    'Detalle entrada',
                    'Fachada',
                    'Hall de entrada',
                    'Parque infantil',
                    'Pasillos de habitaciones',
                    'Recepción',
                    'Vista al exterior',
                    'Vista al interior',
                );
                break;
            case 'Aparcamiento':
                $response = array(
                    'Aparcamiento',
                    'Detalle del Aparcamiento'
                );
                break;
            case 'Gimnasio':
                $response = array(
                    'Piscina interior',
                    'Piscina exterior',
                    'Sala de actividades',
                    'Sala de máquinas',
                );
                break;
            case 'Recepción':
                $response = array(
                    'Atención al cliente',
                    'Atención VIP',
                    'Concierge',
                    'Guest service',
                    'Recepción'
                );
                break;
            case 'Guarderia':
                $response = array(
                    'Kids Club',
                    'Piscina infantil'
                );

        }
        return $response;
    }

    private function checkGalleries(Property $property, &$galleries)
    {
        $response = array();
        foreach ($property->getServices() as $currentService) {
            if ($currentService->getType()->getRoot()) {

                $flag = false;
                foreach ($galleries as &$currentGallery) {
                    if ($currentGallery['nameGallery'] == $currentService->getType()->getRoot()->getTitle()) {
                        $flag = true;
                        break;
                    }
                }

                if (!$flag) {

                    $flag = false;
                    foreach ($response as &$currentGalleryAux) {
                        if ($currentGalleryAux['nameGallery'] == $currentService->getType()->getRoot()->getTitle()) {
                            $flag = true;
                            array_push($currentGalleryAux['features'],$currentService->getType()->getTitle());
                            break;
                        }
                    }

                    if ($flag) {
                        $auxGallery = $this->getDefaultGallery($currentService->getType()->getRoot()->getTitle());
                        if (!empty($auxGallery)) {
                            $auxGallery['idGallery'] = $currentService->getId();
                            $auxGallery['typeGallery'] = 'otherGalleries';
                            $auxGallery['nameGallery'] = $currentService->getType()->getRoot()->getTitle();
                            $auxGallery['loadedImages'] = array();
                            $auxGallery['features'] = array();

                            array_push($auxGallery['features'], $currentService->getType()->getTitle());
                            array_push($response, $auxGallery);
                        }
                    }
                }
            }
        }
    }
}