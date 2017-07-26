<?php
namespace Navicu\Core\Application\UseCases\Admin\PropertyCRUD\UploadImage;

use Navicu\Core\Application\Contract\ManagerImageInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Model\Entity\Document;
use Navicu\Core\Domain\Model\Entity\LogsUser;
use Navicu\Core\Domain\Model\Entity\PropertyImagesGallery;
use Navicu\Core\Domain\Model\Entity\RoomImagesGallery;
use Navicu\Core\Domain\Model\ValueObject\Slug;
use Navicu\Core\Domain\Adapter\CoreSession;

/**
 * La siguiente clase implementa el handler del caso de uso "UploadImage"
 * Subir una imagen de un establecimiento
 *
 * Class UploadImage
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 17/11/2015
 */
class UploadImageHandler implements Handler
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
            $responseArray = $this->uploadImageRoom($data);
            if ($responseArray)
                return new ResponseCommandBus(200,'Ok',$responseArray);
            else
                return new ResponseCommandBus(404, 'Bad Request');

        } else if ($data['typeGallery'] == 'otherGalleries') {
            $responseArray = $this->uploadImageGallery($data);
            if ($responseArray)
                return new ResponseCommandBus(201, 'Ok', $responseArray);
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
    private function uploadImageRoom($data)
    {
        $rpRoom = $this->rf->get('Room');
        $room = $rpRoom->findOneBySlugRoom($data['slug'],$data['idGallery']);
        $existsProfileImage = true;

        if ($room) {

            $nameFile =
                'navicu-reserva-'.
                $data['slug'].'-';

            $folder1 = $room->getProperty()->getSlug();
            $folder2 = 'rooms';

            $roomType = $room->getType();
            $roomName = !is_null($roomType->getParent()) ?
                $roomType->getParent()->getTitle() . ' ' . $roomType->getTitle() :
                $roomType->getTitle();

            $folder3 = Slug::generateSlug($roomName);

            //Se crea el nombre de la ruta del archivo
            $path = 'property/' .
                $folder1 . '/' .
                $folder2 . '/' .
                'habitacion-'.
                $folder3 . '/';

            $document = new Document();
            $document->setFile($data['file']);
            $document->setName('');
            $document->setFileName($path.'/'.$nameFile);

            $roomGallery = new RoomImagesGallery();
            $roomGallery->setImage($document);
            $roomGallery->setRoom($room);
            $roomGallery->setOrderGallery($data['orderGallery']);

            $room->addImagesGallery($roomGallery);

            $document->upload('image', $path, $nameFile);
            //Si no tiene imagen de perfil la habitación
            //Se asigna automaticamente la imagen a subir
            if (!$room->getProfileImage()) {
                $existsProfileImage = false;
                $room->setProfileImage($roomGallery);
            }
            $rpRoom->save($room);
            $this->managerImage->generateImages($document->getFileName());

            /* Crear el la imagen del perfil si no existia */
            if (!$existsProfileImage) {
                $action = "update";
                $resource = "profile room image";
                $this->updateImageRoomOrServiceToLog($room, $this->rf, $action, $resource);
            }
            /* Info para el log */
            $action = "update";
            $resource = "room image";
            $this->updateImageRoomOrServiceToLog($room, $this->rf, $action, $resource);

            $responseArray = array();
            $responseArray['name'] = $document->getName();
            $responseArray['path'] = $document->getFileName();
            $responseArray['idImage'] = $document->getId();
            return $responseArray;
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
    private function uploadImageGallery($data)
    {
        $rpPropertyGallery = $this->rf->get('PropertyGallery');
        $propertyGallery = $rpPropertyGallery->findOneBySlugGallery($data['slug'],$data['idGallery']);

        if ($propertyGallery) {

            $nameFile =
                'navicu-reserva-'.
                $data['slug'].'-';

            $folder1 = $propertyGallery->getProperty()->getSlug();
            $folder2 = 'othergalleries';

            $folder3 = Slug::generateSlug(
                $propertyGallery->getType()->getTitle()
            );

            //Se crea el nombre de la ruta del archivo
            $path = 'property/' .
                $folder1 . '/' .
                $folder2 . '/' .
                $folder3 . '/';

            $document = new Document();
            $document->setFile($data['file']);
            $document->setName('');
            $document->setFileName($path);

            $propertyImage = new PropertyImagesGallery();
            $propertyImage->setImage($document);
            $propertyImage->setPropertyGallery($propertyGallery);
            $propertyImage->setOrderGallery($data['orderGallery']);

            $propertyGallery->addImagesGallery($propertyImage);
            $document->upload('image', $path, $nameFile);
            $rpPropertyGallery->save($propertyGallery);

            $this->managerImage->generateImages($document->getFileName());

            /* Informacion para el Log */
            $action = "update";
            $resource = "service image";
            $this->updateImageRoomOrServiceToLog($propertyGallery, $this->rf, $action, $resource);

            $responseArray = array();
            $responseArray['name'] = $document->getName();
            $responseArray['path'] = $document->getFileName();
            $responseArray['idImage'] = $document->getId();
            return $responseArray;
        }

        return false;
    }

    /**
     * Funcion para subir la imagen de la Habitacion o de un Servicio
     *
     * @param object $roomOrService, habitacion o servicio que esta siendo subida
     * @param $rf
     * @param string $action, qué accion se llevara a cabo
     * @param string $resource, qué se está modificando
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 17/06/2016
     */
    public function updateImageRoomOrServiceToLog($roomOrService, $rf, $action, $resource) {
        if ($resource == "profile room image")
            $document = $roomOrService->getProfileImage()->getImage();
        else
            $document = $roomOrService->getImagesGallery()[count($roomOrService->getImagesGallery())-1]->getImage();

        $dataToLog['resource'] = $resource;
        $dataToLog['property'] = $roomOrService->getProperty();
        $dataToLog['user'] = CoreSession::getUser();
        $dataToLog['action'] = $action;
        $dataToLog["idResource"] = $document->getId();
        $dataToLog["description"]['path'] = $document->getFileName();

        $logsUser = new LogsUser();
        $logsUser->updateObject($dataToLog);
        $rf->get('LogsUser')->save($logsUser);
    }
}