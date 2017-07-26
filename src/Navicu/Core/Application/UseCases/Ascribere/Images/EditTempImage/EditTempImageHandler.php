<?php

namespace Navicu\Core\Application\UseCases\Ascribere\Images\EditTempImage;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

/**
* Clase para ejecutar el caso de uso EditTempServices
* @author Freddy Contreras <freddycontreras3@gmail.com>
* @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
* @version 30/06/2015
*/

class EditTempImageHandler implements Handler
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

        // Si existe el usuario temporal
        if ($tempOwner) {

            $nameProperty = $tempOwner->getPropertyForm()['name'];
            $registerImagesBD = $tempOwner->getGalleryForm();

            //Se busca si existe la imagen a eliminar
            $editImage = $this->findImage($registerImagesBD);

            //Si existe el archivo en la carpeta de imagenes y en la BD
            if ($editImage) {
                
                $tempOwner->setGalleryForm($registerImagesBD);
                $rpTempOwner->save($tempOwner);
                return new ResponseCommandBus(201,'OK', $editImage); 

            } else {
                return new ResponseCommandBus(400,'Bad Request'); 
            }        

        } else {
            return new ResponseCommandBus(401,'Unauthorized ');  
        }
    }

    /**
    * La función se encarga de buscar en los datos de la BD la imagen según un path
    * y busqueda de los archivos
    *
    * @param Array $dataImages
    * @param string $nameProperty
    * @return boolean
    * @author Freddy Contreras <freddycontreras3@gmail.com>
    * @version 30/06/2015
    */
    private function findImage(&$dataImages)
    {
        $response = false;
        $arrayImages = false;

        // utilizando los métodos del comando
        $path = $this->command->getPath();
        $gallery = $this->command->getGallery();
        $subGallery = $this->command->getSubGallery();  
        $name = $this->command->getName();
        $idSubGallery = $this->command->getIdSubGallery();

        if (array_key_exists($gallery, $dataImages)) {

            foreach ($dataImages[$gallery] as &$currentImage) {
                if ($currentImage['idSubGallery'] == $idSubGallery and 
                    $currentImage['subGallery'] == $subGallery) {

                    $arrayImages = &$currentImage;
                    break;
                }
            }            
        }

        if ($arrayImages) {
            foreach ($arrayImages['images'] as $key => &$currentImage) {
                if ($currentImage['path'] == $path) {
                    $currentImage['name'] = $name;
                    $response = $path;
                    break;
                }
            }
        }

        return $response;
    }
}   