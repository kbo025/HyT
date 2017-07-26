<?php
namespace Navicu\Core\Application\UseCases\Admin\PropertyCRUD\DeleteFavorite;

use Navicu\Core\Application\Contract\ManagerImageInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Model\Entity\LogsUser;

/**
 * La siguiente clase implementa el handler del caso de uso "DeleteFavorite"
 * Eliminar una imagen favorita del establecimiento
 *
 * Class DeleteFavorite
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 17/11/2015
 */
class DeleteFavoriteHandler implements Handler
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
        $rpPropertyFavoriteImages = $this->rf->get('PropertyFavoriteImages');
        $dataToLog['resource'] = "Image";
        $dataToLog['slug'] = $data['slug'];

        if ($data['deleteType'])
            $favoriteImage = $rpPropertyFavoriteImages->findOneBySlugImage(
                $data['slug'],
                $data['idFavorite']
            );
        else
            $favoriteImage = $rpPropertyFavoriteImages->findOneBySlugId(
                $data['slug'],
                $data['idFavorite']
            );
        if ($favoriteImage) {
            $property = $favoriteImage->getProperty();

            // Si es la imagen de perfil se debe agregar la siguiente imagen favorita
            if ($property->getProfileImage() == $favoriteImage) {
                $pathImageFavorite = $favoriteImage->getImage()->getFileName();

                $this->managerImage->deleteFilter($pathImageFavorite,'images_email');

                $favoriteImages = $rpPropertyFavoriteImages
                    ->findBySlugNotEqualId(
                        $data['slug'],
                        $favoriteImage->getId()
                    );

                if(count($favoriteImages) > 0) {
                    $newPathFavorite = $favoriteImages[0]->getImage()->getFileName();
                    $property->setProfileImage($favoriteImages[0]);
                    $this->managerImage->generateFilter($newPathFavorite, 'images_email');
                }
                else {
                    $property->setProfileImage(null);
                }
                /* Data al log para actualizar la imagen del perfil del establecimiento*/
                $action = "update";
                $resource = "profile property image";
                $imageToUpdate = (count($favoriteImages) > 0) ? $favoriteImages[0] : null;
                $this->saveDeleteImageToLog($imageToUpdate, $resource, $action, $property, $this->rf);

                $rpProperty = $this->rf->get('Property');
                $rpProperty->save($property);
            }
            /* Datos para el log cuando la imagen no es de perfil*/
            $resource = "favorite image";
            $action = "delete";
            $this->saveDeleteImageToLog($favoriteImage, $resource, $action, $property, $this->rf);

            $rpPropertyFavoriteImages->delete($favoriteImage);
            return new ResponseCommandBus(200,'Ok');
        }

        return new ResponseCommandBus(404,'No found');
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