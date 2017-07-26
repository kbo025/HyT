<?php
namespace Navicu\Core\Application\UseCases\Admin\RemoveRoom;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ManagerImageInterface;
use Navicu\Core\Domain\Model\ValueObject\Slug;

/**
 * El siguiente handler Elimina una habitación de un establecimiento.
 *
 * Class RemoveRoomHandler
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class RemoveRoomHandler implements Handler
{
    /**
     *   Instancia del repositoryFactory
     *   @var RepositoryFactory $rf
     */
    protected $rf;

    /**
    * Manejador de imagenes
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
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     * 
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * 
     * @return ResponseCommandBus
     */
    public function handle( Command $command, RepositoryFactoryInterface $rf )
    {
        $this->rf = $rf;
		$data = $command->getRequest();
		$room = $rf->get("Room")->find($data["id"]);

		if (!$room)
			return new ResponseCommandBus(400, 'Ok', null);

		$property = $room->getProperty();
		$profileImageProperty = $property->getProfileImage()->getImage();
		
		$room->setProfileImage(null);
		$imagesGallery = $room->getImagesGallery();
		foreach ($imagesGallery as $image) {
			if ($image->getImage()->getId() == $profileImageProperty->getId()) {
				$property->setProfileImage(null);
			}
			$propertyGallery = $image->getImage()->getPropertyFavoriteImages();
			if ($propertyGallery) {
				$property->removePropertyFavoriteImage($propertyGallery);
				$rf->get("Room")->remove($propertyGallery);
			}

			$rf->get("Room")->remove($image->getImage());
			$room->removeImagesGallery($image);
			$rf->get("Room")->remove($image);
		}
		$room->setIsActive(false);
		//$property->setActive(false);
		$rf->get("Property")->save($property);
		
		$imagesEmail = $_SERVER['DOCUMENT_ROOT']."/uploads/images/images_email/property/".$property->getSlug()."/rooms/". Slug::generateSlug($room->getName());
		$imagesMd = $_SERVER['DOCUMENT_ROOT']."/uploads/images/images_md/property/".$property->getSlug()."/rooms/". Slug::generateSlug($room->getName());
		$imagesXs = $_SERVER['DOCUMENT_ROOT']."/uploads/images/images_xs/property/".$property->getSlug()."/rooms/". Slug::generateSlug($room->getName());
		$imagesOriginal = $_SERVER['DOCUMENT_ROOT']."/uploads/images/images_original/property/".$property->getSlug()."/rooms/". Slug::generateSlug($room->getName());
		$imagessSm = $_SERVER['DOCUMENT_ROOT']."/uploads/images/images_sm/property/".$property->getSlug()."/rooms/". Slug::generateSlug($room->getName());

		$this->rrmdir($imagesEmail);
		$this->rrmdir($imagesMd);
		$this->rrmdir($imagesXs);
		$this->rrmdir($imagesOriginal);
		$this->rrmdir($imagessSm);

        $this->refactorRoomNamesForProperty($rf,$property->getId(),$room->getType()->getId());

        return new ResponseCommandBus(200, 'Ok',$imagesEmail);
    }

	/**
     * Función es usada para eliminar una carpeta y junto con ella
     * todo los archivos en ella se encuentr.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     * 
     * @param string $dir
     */
	function rrmdir($dir) { 
		if (is_dir($dir)) { 
			$objects = scandir($dir);

			foreach ($objects as $object) { 
				if ($object != "." && $object != "..") { 
					if (filetype($dir."/".$object) == "dir") {
						rrmdir($dir."/".$object);
					} else {
						unlink($dir."/".$object); 
					}
				} 
			}

			reset($objects); 
			rmdir($dir); 
		} 
	}

	/**
	 * esta funcion verifica las habitaciones de un establecimiento y las renombra dependiendo 
     *
     */
	public function refactorRoomNamesForProperty($rf,$propertyId,$typeId)
	{
		$rep = $rf->get('Room');
		$rooms = $rep->findActiveRoomsByIdProperty($propertyId,$typeId);
		if (count($rooms) > 0) {
			$persistRoom = [];
			$unique = true;
			foreach( $rooms as $room ) {
				$room->setUniqueType($unique);
				$room->generateName();
                //$room->changeNameGallery($rf);
				$persistRoom[] = $room;
				$unique = false;
				$this->updateGallery($room);
			}
			$rep->save($persistRoom);
		}
	}

    /**
     * La siguiente función se encarga de actualizar los valores
     * de las galerías si se sustituye el tipo de habitación
     *
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $dataGallery
     * @param $newGalleryId
     * @param $oldGalleryId
     * @param $oldRoomName
     * @param $newNameRoom
     */
    private function updateGallery($room)
    {
		$imagesGallery = $room->getImagesGallery();
        $rename = [];
		foreach ($imagesGallery as $image) {
            $document = $image->getImage();
            $current = [
                'new' => 'property/'.$room->getPropertyId()->getSlug().'/rooms/'.Slug::generateSlug($room->getName()),
                'old' => implode('/',explode('/',$document->getFileName(),-1)),  
            ];
            $fileName = $current['new'].'/'.$document->getName();
            $document->setFileName($fileName);
            $persist[] = $document;
            $rename[] = $current;
		}

        foreach ($rename as $current)
            $this->managerImage->changePath($current['old'], $current['new'].'temp');

        foreach ($rename as $current)
            $this->managerImage->changePath($current['new'].'temp', $current['new']);

        if(!empty($persist))
            $this->rf->get('Room')->save($persist);
    }
}