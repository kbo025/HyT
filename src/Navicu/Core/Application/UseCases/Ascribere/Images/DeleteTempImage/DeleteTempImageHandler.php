<?php

namespace Navicu\Core\Application\UseCases\Ascribere\Images\DeleteTempImage;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Model\Entity\Document;
use Navicu\Core\Application\Contract\ResponseCommandBus;

/**
* Clase para ejecutar el caso de uso DeleteTempImage
* @author Freddy Contreras <freddycontreras3@gmail.com>
* @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
* @version 30/06/2015
*/

class DeleteTempImageHandler implements Handler
{   
    /**
    * @var Representa el comando de la clase
    */
    private $command;

    /**
    * @var Representa la RepositorioFactory de la clase
    */
    private $rf;

    /**
     * Ejecuta el caso de uso 'Elimina una imagen en espacio Temporal'
     *
     * @param Command $command Objeto Command contenedor de la soliccitud del usuario
     * @param RepositoryFactoryInterface  $rf
     *
     * @return \Navicu\Core\Application\Contract\ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf = null)
    {
        $this->command = $command;
        $this->rf = $rf;
        
        $rpTempOwner = $this->rf->get('TempOwner');

        //Se obtiene los datos del usuario 
        $tempOwner = $rpTempOwner->findOneByArray(
            array('slug' => $this->command->getSlug()));

        //Se obtiene los datos de la galería de imagenes
        $registerImagesBD = $tempOwner->getGalleryForm();

        //Se busca si existe la imagen a eliminar
        $deleteImage = $this->findImage(
            $registerImagesBD,
            $command->getPath(),
            $command->getGallery(),
            $command->getSubGallery(),
            $command->getIdSubGallery());

        //Se busca el archivo en la carpeta de imagenes
        $file = $_SERVER['DOCUMENT_ROOT'].'/uploads/images/images_original/'.$command->getPath();

        //Si existe el archivo en la carperta de imagenes y en la BD
        //Se elimina la imagen
        if ($deleteImage) {
            $tempOwner->setGalleryForm($registerImagesBD);
            $rpTempOwner->save($tempOwner);

            if (file_exists($file))
                unlink($file);

            //Eliminando la imagen de tamaño XS
            $fileXS = $_SERVER['DOCUMENT_ROOT'] . '/uploads/images/images_xs/' . $command->getPath();

            if (file_exists($fileXS))
                unlink($fileXS);

            //Eliminando la imagen de tamaño SM
            $fileSM = $_SERVER['DOCUMENT_ROOT'] . '/uploads/images/images_sm/' . $command->getPath();

            if (file_exists($fileSM))
                unlink($fileSM);

            //Eliminando la imagen de tamañoMD
            $fileMD = $_SERVER['DOCUMENT_ROOT'] . '/uploads/images/images_md/' . $command->getPath();

            if (file_exists($fileMD))
                unlink($fileMD);
        }

        return new ResponseCommandBus(201, 'OK');
    }


    /**
    * La función se encarga de buscar en los datos de la BD la imagen según un path
    *
    * @param Array $dataImages
    * @param String $path
    * @param String $gallery
    * @param String $subGallery
    * @return boolean
    * @author Freddy Contreras <freddycontreras3@gmail.com>
    * @version 30/06/2015
    */
    private function findImage(&$dataImages, $path, $gallery, $subGallery, $idSubGallery)
    {
        $response = false;
        $arrayImages = false;

        if (array_key_exists($gallery, $dataImages)) {
            foreach ($dataImages[$gallery] as &$currentGallery) {
                if ($currentGallery['idSubGallery'] == $idSubGallery and
                    $currentGallery['subGallery'] == $subGallery) {

                    $arrayImages = &$currentGallery;
                    break;
                }
            }
            //if(array_key_exists($subGallery, $dataImages[$gallery]))
               // $arrayImages = &$dataImages[$gallery][$subGallery];
        }
            

        if ($arrayImages) {

            foreach ($arrayImages['images'] as $key => $currentImage) {                
                if ($currentImage['path'] == $path) {
                    $this->findFavoriteImage($dataImages, $path, $subGallery);
                    array_splice($arrayImages['images'], $key, 1);
                    //unset($arrayImages['images'][$key]);
                    $response = true;
                    break;
                }
            }
        }

        return $response;
    }

    /**
    * La función se encarga de encontrar las imagen en las imagenes favoritas
    * Y actualizar sus datos.
    *
    * @author Freddy Contreras <freddycontreras3@gmail.com>
    * @param array $registerImagesBD
    * @param string $path
    * @param string $newPath
    * @param string $subGallery
    * @return void
    * @version 07/07/2015
    */
    private function findFavoriteImage(&$registerImageBD, $path, $subGallery)
    {        
        if (isset($registerImageBD['favorites'])) {
            foreach ($registerImageBD['favorites'] as $key => $currentImage) {
                if ($currentImage['path'] == $path and
                    $currentImage['subGallery'] == $subGallery) {
                    
                    unset($registerImageBD['favorites'][$key]);
                    break;
                }
            }
        }
    }
}   