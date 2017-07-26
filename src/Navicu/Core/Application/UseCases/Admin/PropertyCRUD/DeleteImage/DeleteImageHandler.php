<?php
namespace Navicu\Core\Application\UseCases\Admin\PropertyCRUD\DeleteImage;

use Navicu\Core\Application\Contract\ManagerImageInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Model\Entity\Document;
use Navicu\Core\Domain\Model\Entity\PropertyFavoriteImages;
use Navicu\Core\Domain\Model\Entity\PropertyImagesGallery;
use Navicu\Core\Domain\Model\Entity\RoomImagesGallery;
use Navicu\Core\Domain\Model\ValueObject\Slug;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Model\Entity\LogsUser;

/**
 * La siguiente clase implementa el handler del caso de uso "DeleteImage"
 * Subir una imagen de un establecimiento
 *
 * Class DeleteImage
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 17/11/2015
 */
class DeleteImageHandler implements Handler
{
    /**
     * Instancia del repositoryFactory
     *
     * @var RepositoryFactory $rf
     */
    protected $rf;

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
     * @version 18/11/2015
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $this->rf = $rf;
        $data = $command->getRequest();
        if ($data['typeGallery'] == 'rooms') {
            if ($this->DeleteImageRoom($data))
                return new ResponseCommandBus(200, 'Ok');
            else
                return new ResponseCommandBus(404, 'Bad Request');

        } else if ($data['typeGallery'] == 'otherGalleries') {
            if ($this->DeleteImageGallery($data))
                return new ResponseCommandBus(201, 'Ok');
            else
                return new ResponseCommandBus(404, 'Bad Request');
        }

        return new ResponseCommandBus(404, 'Bad Request');
    }

    /**
     * La función carga en el sistema una imagen de la galería de tipo habitación
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $data
     * @return bool
     * @version 20/11/2015
     */
    private function deleteImageRoom($data)
    {
        $rpRoomImageGallery = $this->rf->get('RoomImagesGallery');
        $roomImage = $rpRoomImageGallery->findOneBySlugRoomImage(
            $data['slug'],
            $data['idGallery'],
            $data['idImage']);

        // Si existe la  imagen y no es la imagen de perfil
        // no se eliminará la imagen de perfil de la habitación
        if ($roomImage) {

            /*Datos para el log*/
            $rpPropertyFavoriteImages = $this->rf->get('PropertyFavoriteImages');

            $favoriteImage = $rpPropertyFavoriteImages->findOneBySlugImage(
                $data['slug'],
                $data['idImage']);
            $property = $roomImage->getRoom()->getProperty();

            //Si la imagen a eliminar es una favorita
            if ($favoriteImage) {
                /* Dato para el log para eliminar una imagen y ademas es favorita*/
                $resource = "favorite room image";
                $action = "delete";
                $this->saveDeleteImageToLog($favoriteImage, $resource, $action, $property, $this->rf);

                $this->checkFavorites($favoriteImage);
                $rpPropertyFavoriteImages->delete($favoriteImage);
            }

            $room = $roomImage->getRoom();

            // Si la imagen a eliminar es la foto de perfil de la habitación
            // Se debe asignar una nueva imagen de perfil
            if ($room->getProfileImage() == $roomImage) {

                $roomsImage = $rpRoomImageGallery
                    ->findByRoomNotEqualId(
                        $room->getId(),$roomImage->getId());

                $rpRoom = $this->rf->get('Room');

                /* Datos para el log, para borrar la imagen del perfil existente*/
                $resource = "profile room image";
                $action = "delete";
                $this->saveDeleteImageToLog($roomImage, $resource, $action, $property, $this->rf);

                if (count($roomsImage) > 0) {
                    $room->setProfileImage($roomsImage[0]);
                }
                else {
                    /* Actualizar la imagen del perfil cuando no existe ninguna otra en la galeria*/
                    $roomImageProfile = null;
                    $room->setProfileImage(null);
                }
                $rpRoom->save($room);

                $resource = "profile room image";
                $action = "update";
                $this->saveDeleteImageToLog($room->getProfileImage(), $resource, $action, $property, $this->rf);
            }

            /* Si no fue ni favorita ni imagen de perfil se elimina como una imagen mas*/
            if ( !isset($action) ) {
                $resource = "room image";
                $action = "delete";
                $this->saveDeleteImageToLog($roomImage, $resource, $action, $property, $this->rf);
            }
            $document = $roomImage->getImage();
            $pathImage = $document->getFileName();

            //Se elimina de la entidad document
            $rpRoomImageGallery->delete($roomImage);
            $this->managerImage->deleteImages($pathImage);

            //Se elimina de la entidad document
            $rpDocument = $this->rf->get('Document');
            $rpDocument->delete($document);

            return true;
        }

        return false;
    }

    /**
     * La función carga en el sistema una imagen de las otras galerías
     * como Zonas Comunes, Gimnasio, Spa, etc.
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $data
     * @return bool
     * @version 20/11/2015
     */
    private function deleteImageGallery($data)
    {
        $rpPropertyImagesGallery = $this->rf->get('PropertyImagesGallery');
        $propertyImageGallery = $rpPropertyImagesGallery
            ->findOneBySlugGalleryImage(
                $data['slug'],
                $data['idGallery'],
                $data['idImage']
            );
        $property = $propertyImageGallery->getPropertyGallery()->getProperty();
        if ($propertyImageGallery) {

            $rpPropertyFavoriteImages = $this->rf->get('PropertyFavoriteImages');
            $favoriteImage = $rpPropertyFavoriteImages->findOneBySlugImage(
                $data['slug'],
                $data['idImage']);

            // Si la imagen a eliminar es una foto de favorita del establecimiento
            if ($favoriteImage) {
                $this->checkFavorites($favoriteImage);
                /* Datos para el log de los servicios en caso que la imagen sea favorita */
                $resource = "favorite service image";
                $action = "delete";
                $this->saveDeleteImageToLog($favoriteImage, $resource, $action, $property, $this->rf);

                $rpPropertyFavoriteImages->delete($favoriteImage);
            }

            $document = $propertyImageGallery->getImage();
            $pathImage = $document->getFileName();

            /* Si la imagen no fue favorita */
            if (!isset($action)) {
                $resource = "service image";
                $action = "delete";
                $this->saveDeleteImageToLog($propertyImageGallery, $resource, $action, $property, $this->rf);
            }

            //Se elimina la galería de la habitación
            $rpPropertyImagesGallery->delete($propertyImageGallery);
            $this->managerImage->deleteImages($pathImage);

            //Se elimina de la entidad document
            $rpDocument = $this->rf->get('Document');
            $rpDocument->delete($document);

            return true;
        }

        return false;
    }

    /**
     * La siguiente función se encarga de chequear
     * si la imagen a eliminar pertenece a la imagen de perfil
     * del establecimiento
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param PropertyImagesGallery $favoriteImage
     * @param Property $property
     * @version 12/01/2016
     */
    private function checkFavorites(PropertyFavoriteImages $favoriteImage)
    {
        $property = $favoriteImage->getProperty();
        $profileImage = $property->getProfileImage();
        $rpPropertyFavoriteImages = $this->rf->get('PropertyFavoriteImages');

        if ($profileImage == $favoriteImage) {

            $pathImage = $favoriteImage->getImage()->getFileName();
            $this->managerImage->deleteFilter($pathImage,'images_email');

            $favoritesImages = $rpPropertyFavoriteImages
                ->findBySlugNotEqualId(
                    $property->getSlug(),
                    $favoriteImage->getId()
                );

            if (count($favoritesImages) > 0) {
                $newPathFavorite = $favoritesImages[0]->getImage()->getFileName();
                $property->setProfileImage($favoritesImages[0]);
                $this->managerImage->generateFilter($newPathFavorite, 'images_email');
            }
            else
                $property->setProfileImage(null);

            $rpProperty = $this->rf->get('Property');
            $rpProperty->save($property);
        }
    }

    /**
     * Funcion encargada de guardar las imagenes del room al log
     *
     * @param object $image, imagen que se quiere guardar en la base de datos de log
     * @param string $resource, definicion sobre la entidad que esta sufriendo el cambio
     * @param string $action, accion CRUD que se esta llevando a cabo
     * @param $property
     * @param $rf
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 21/06/2016
     */
    public function saveDeleteImageToLog($image, $resource, $action, $property, $rf) {
        $dataToLog['action'] = $action;
        $dataToLog['resource'] = $resource;
        $dataToLog['user'] = CoreSession::getUser();
        $dataToLog['property'] = $property;
        if ($action == "delete")
            $dataToLog['idResource'] = null;
        else
            $dataToLog['idResource'] = !is_null($image) ? $image->getImage()->getId() : null;
        $dataToLog["description"]['path'] = !is_null($image) ? $image->getImage()->getFileName() : null;

        $logsUser = new LogsUser();
        $logsUser->updateObject($dataToLog);
        $rf->get('LogsUser')->save($logsUser);
    }
}