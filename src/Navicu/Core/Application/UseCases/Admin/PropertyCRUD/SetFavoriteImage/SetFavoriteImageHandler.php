<?php
namespace Navicu\Core\Application\UseCases\Admin\PropertyCRUD\SetFavoriteImage;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Model\Entity\Document;
use Navicu\Core\Domain\Model\Entity\Property;
use Navicu\Core\Domain\Model\Entity\PropertyFavoriteImages;
use Navicu\Core\Application\Contract\ManagerImageInterface;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Model\Entity\LogsUser;


/**
 * La siguiente clase implementa el handler del caso de uso "SetFavoriteImage"
 * Guardar una imagen como favorito
 *
 * Class SetFavoriteImage
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 23/11/2015
 */
class SetFavoriteImageHandler implements Handler
{
    private $rf;

    /**
     * Instancia del ManagerImage
     *
     * @var ManagerImageInterface
     */
    protected $managerImage;

    /**
     * Método Get de $managerImage
     *
     * @param ManagerImageInterface $managerImage
     */
    public function setManagerImage(ManagerImageInterface $managerImage)
    {
        $this->managerImage = $managerImage;
    }

    /**
     * Ejecuta las tareas solicitadas
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 23/11/2015
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $this->rf = $rf;
        $data = $command->getRequest();
        $error = null;
        if ($data['typeGallery'] == 'rooms') {
            $response = $this->setFavoriteRoom($data, $error);
            if ($response)
                return new ResponseCommandBus(200,'Ok', $response);
            else
                return new ResponseCommandBus(400,'Bad Request', $error);

        } else if ($data['typeGallery'] == 'otherGalleries') {
            $response = $this->setFavoriteGallery($data, $error);
            if ($response)
                return new ResponseCommandBus(200, 'Ok', $response);
            else
                return new ResponseCommandBus(400,'Bad Request', $error);
        }

        return new ResponseCommandBus(404,'Not Found');
    }

    /**
     * La siguiente función se encarga de procesar una imagen favorita
     * que pertenece a la galería de habitaciones
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $data
     * @param $error
     * @return bool
     * @version 24/11/2015
     */
    private function setFavoriteRoom($data, &$error)
    {
        $rpRoomImageGallery = $this->rf->get('RoomImagesGallery');

        $roomImage = $rpRoomImageGallery->findOneBySlugRoomImage(
            $data['slug'],
            $data['idGallery'],
            $data['idImage']);

        if ($roomImage) {

            $property = $roomImage->getRoom()->getProperty();

            if (count($property->getPropertyFavoriteImages()) < 8 ) {
                /* Datos para el log */
                $resource = "favorite room image";
                $this->saveFavoriteToLog($roomImage, $resource, $this->rf, $property);

                if (count($property->getPropertyFavoriteImages()) == 0 ) {
                    $resource = "profile property image";
                    $this->saveFavoriteToLog($roomImage, $resource, $this->rf, $property);
                }
                return $this->saveFavorite($property, $roomImage->getImage(),$data['orderGallery']);
            }
            $error = 'No se puede subir la imagen la galería favoritos esta completa';
        } else
            $error = 'la galería o imagen no existe';

        return false;
    }

    /**
     * La siguiente función se encarga de procesar una imagen favorita
     * que pertenece a las "otras galerías" del establecimiento
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $data
     * @param $error
     * @return bool
     * @version 24/11/2015
     */
    private function setFavoriteGallery($data, &$error)
    {
        $rpPropertyImagesGallery = $this->rf->get('PropertyImagesGallery');

        $imageGallery = $rpPropertyImagesGallery->findOneBySlugGalleryImage(
            $data['slug'],
            $data['idGallery'],
            $data['idImage']
        );

        if ($imageGallery) {

            $property = $imageGallery->getPropertyGallery()->getProperty();

            if (count($property->getPropertyFavoriteImages()) < 8) {
                /* Datos para el log */
                $resource = "favorite service image";
                $this->saveFavoriteToLog($imageGallery, $resource, $this->rf, $property);

                return $this->saveFavorite($property, $imageGallery->getImage(), $data['orderGallery']);
            }
            $error = 'No se puede subir la imagen la galería favoritos esta completa';
        } else
            $error = 'la galería o imagen no existe';

        return false;
    }

    /**
     * Función se encarga de persistir una imagen favorita
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param Property $property
     * @param Document $image
     * @param $orderGallery
     * @version 24/11/2015
     */
    private function saveFavorite(Property $property, Document $image, $orderGallery)
    {
        $rpProperty = $this->rf->get('Property');
        $propertyFavorite = new PropertyFavoriteImages();
        $propertyFavorite->setProperty($property);
        $propertyFavorite->setImage($image);
        $propertyFavorite->setOrderGallery($orderGallery);
        $property->addPropertyFavoriteImage($propertyFavorite);

        if (!$property->getProfileImage()) {
            $property->setProfileImage($propertyFavorite);
            $this->managerImage->generateFilter($image->getFileName(),'images_email');
        }

        $rpProperty->save($property);
        return $propertyFavorite->getId();
    }

    /**
     * Funcion encargada de guardar las imagenes de la galleria o room al log
     *
     * @param object $roomImageOrService, objeto bien sea imagen de room o de gallery
     * @param string $resource, definicion sobre la entidad que esta sufriendo el cambio
     * @param $rf
     * @param $property
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 21/06/2016
     */
    public function saveFavoriteToLog($roomImageOrService, $resource, $rf, $property) {
        $dataToLog['action'] = "update";
        $dataToLog['resource'] = $resource;
        $dataToLog['user'] = CoreSession::getUser();
        $dataToLog['property'] = $property;
        $dataToLog['idResource'] = $roomImageOrService->getImage()->getId();
        $dataToLog["description"]['path'] = $roomImageOrService->getImage()->getFileName();

        $logsUser = new LogsUser();
        $logsUser->updateObject($dataToLog);
        $rf->get('LogsUser')->save($logsUser);
    }
}