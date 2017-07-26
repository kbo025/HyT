<?php
namespace Navicu\Core\Application\UseCases\Admin\PropertyCRUD\SortGalleries;

use Navicu\Core\Application\Contract\ManagerImageInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Model\Entity\LogsUser;

/**
 * La siguiente clase implementa el handler del caso de uso "SortGalleries"
 * Ordenamiento de imagenes de un establecimiento
 *
 * Class SortGalleries
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 17/11/2015
 */
class SortGalleriesHandler implements Handler
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
     * @version 14/01/2016
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {

        $this->rf = $rf;
        //try {
            $data = $command->getRequest();

            $this->setFavorites($data['slug'], $data['favoritesData']);
            $this->setOrderGalleries($data['slug'], $data['galleriesData']);
            return new ResponseCommandBus(201,'Ok');
        /*}  catch (\Exception $e) {
            return new ResponseCommandBus(500,'Internal Server Error',$e);
        }*/
    }

    /**
     * La siguiente función ordena las imagenes favoritas del establecimiento
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $slug
     * @param $favorites
     * @version 15/01/2016
     */
    private function setFavorites($slug, $favorites)
    {
        $rpPropertyFavoriteImages = $this->rf->get('PropertyFavoriteImages');
        $rpProperty = $this->rf->get('Property');

        foreach ($favorites as $currentFavorite) {
            $favoriteImage = $rpPropertyFavoriteImages->findOneBySlugId(
                $slug,
                $currentFavorite['idFavorite']
            );

            if ($favoriteImage) {
                $favoriteImage->setOrderGallery($currentFavorite['orderGallery']);

                // Si es la primera imagen se toma como imagen de perfil
                if ($currentFavorite['orderGallery'] == 0) {
                    $property = $favoriteImage->getProperty();
                    $oldProfileImage = $property->getProfileImage();

                    //Si la imagen de perfil cambio
                    if ($oldProfileImage != $favoriteImage) {

                        //Se elimina la imagen vieja como imagen del correo
                        if ($oldProfileImage) {
                            $oldImagePath = $oldProfileImage->getImage()->getFileName();
                            $this->managerImage->deleteFilter($oldImagePath, 'images_email');
                        }
                        //Se almacena la imagen nueva como imagen del correo
                        $newImagePath = $favoriteImage->getImage()->getFileName();
                        $this->managerImage->generateFilter($newImagePath, 'images_email');

                        //Se asigna nueva imagen de perfil al establecimiento
                        $property->setProfileImage($favoriteImage);
                        $rpProperty->save($property);

                        /* Informacion para el Log */
                        $action = "update";
                        $resource = "profile property image";
                        $typeRoomOrImage = "image";
                        $this->updateImageToLog($favoriteImage, $this->rf, $action, $resource, $typeRoomOrImage);
                    }
                }

                $rpPropertyFavoriteImages->save($favoriteImage);
            }
        }
    }

    /**
     * Función de ordenamiento de las galerías de habitaciones y las otras
     * galerías de una establecimiento
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param string $slug
     * @param array $galleries
     * @version 15/01/2015
     */
    private function setOrderGalleries($slug, $galleries)
    {
        $rpRoomImagesGallery = $this->rf->get('RoomImagesGallery');
        $rpPropertyImagesGallery = $this->rf->get('PropertyImagesGallery');
        $rpRoom = $this->rf->get('Room');
        $roomsToReview = [];
        $logs = [];

        foreach ($galleries as $currentGallery) {
            foreach ($currentGallery['imagesOrder'] as $currentImage) {
                // Si la imagen es de tipo habitación
                if ($currentGallery['typeGallery'] == 'rooms') {
                    $roomImage = $rpRoomImagesGallery->findOneBySlugRoomImage(
                        $slug,
                        $currentGallery['idGallery'],
                        $currentImage['idImage']);
                    //Se actualiza el orden de la imagen
                    if ($roomImage) {
                        $oldRoom = clone ($roomImage->getRoom());
                        $roomImage->setOrderGallery($currentImage['orderGallery']);
                        // Si es la primera imagen se toma como imagen de perfil
                        if ($currentImage['orderGallery'] == 0) {
                            $room = $roomImage->getRoom();
                            $room->setProfileImage($roomImage);
                            $rpRoom->save($room);

                            /* Clonamos el room nuevo y lo metemos en el arreglo junto con el viejo*/
                            $newRoom = clone $room;
                            array_push($roomsToReview, $newRoom);
                            array_push($roomsToReview, $oldRoom);
                            array_push($logs, $roomsToReview);
                            $roomsToReview = [];
                        }
                        $rpRoomImagesGallery->save($roomImage);

                    }

                } else if ($currentGallery['typeGallery'] == 'otherGalleries') {
                    // Si la imagen pertenece a otra galería

                    $imageGallery = $rpPropertyImagesGallery
                        ->findOneBySlugGalleryImage(
                            $slug,
                            $currentGallery['idGallery'],
                            $currentImage['idImage']
                        );
                    // Se actualiza el orden de la imagen
                    if ($imageGallery) {
                        $imageGallery->setOrderGallery($currentImage['orderGallery']);
                        $rpPropertyImagesGallery->save($imageGallery);
                    }
                }
            }
        }
        /* revisamos si existe una imagen nueva para el perfil del property*/
        $this->reviewRooms($logs, $this->rf);
    }

    /**
     * Funcion para subir la imagen de la Habitacion o de la imagen de perfil propiamente
     *
     * @param object $roomOrProfileImage, habitacion o imagen que esta siendo subida
     * @param $rf
     * @param string $action, qué accion se llevara a cabo
     * @param string $resource, qué se está modificando
     * @param string $typeRoomOrImage,que tipo de parametro es, del tipo room o imagen
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 20/06/2016
     */
    public function updateImageToLog($roomOrProfileImage, $rf, $action, $resource, $typeRoomOrImage) {
        if ($typeRoomOrImage == "room")
            $document = $roomOrProfileImage->getProfileImage()->getImage();
        else if ($typeRoomOrImage == "image")
            $document = $roomOrProfileImage->getImage();

        $dataToLog['resource'] = $resource;
        $dataToLog['property'] = $roomOrProfileImage->getProperty();
        $dataToLog['user'] = CoreSession::getUser();
        $dataToLog['action'] = $action;
        $dataToLog["idResource"] = $document->getId();
        $dataToLog["description"]['path'] = $document->getFileName();

        $logsUser = new LogsUser();
        $logsUser->updateObject($dataToLog);
        $rf->get('LogsUser')->save($logsUser);
    }

    /**
     * Funcion encargada de revisar si hubo o no un cambio en las imagenes del perfil del room
     *
     * @param array $arrayOfRooms, arreglo con la primera posicion el room nuevo, y la segunda el room viejo
     * @param object $rf, repository Factory
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 21/06/16
     */
    public function reviewRooms($arrayOfRooms, $rf) {
        foreach ($arrayOfRooms as $room) {
            if ($room[0]->getProfileImage()->getId() != $room[1]->getProfileImage()->getId()) {
                $action = "update";
                $resource = "profile room image";
                $typeRoomOrImage = "room";
                $this->updateImageToLog($room[0], $rf, $action, $resource, $typeRoomOrImage);
            }
        }
    }
}