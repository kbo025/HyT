<?php

namespace Navicu\Core\Application\UseCases\Ascribere\Images\DeleteTempFavoriteImage;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

/**
* Clase para ejecutar el caso de uso DeleteTempFavoriteImage
* @author Freddy Contreras <freddycontreras3@gmail.com>
* @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
* @version 08/07/2015
*/

class DeleteTempFavoriteImageHandler implements Handler
{
    private $command;

    private $rf;

    /**
     * Ejecuta el caso de uso 'Eliminar una imagen favorita en espacio Temporal'
     *
     * @param Command $command Objeto Command contenedor
     *                                             de la soliccitud del usuario
     * @param RepositoryFactoryInterface  $rf
     *
     * @return \Navicu\Core\Application\Contract\ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf = null)
    {
        $this->command = $command;
        $this->rf = $rf;

        $rpTempOwner  = $this->rf->get('TempOwner');
        //Se verifica los datos del usuario
        $tempOwner = $rpTempOwner->findOneByArray(
            array('slug' => $this->command->getSlug()));

        //Si no existe el establecimiento temporal
        if ($tempOwner) {

            $registerImageBD = $tempOwner->getGalleryForm();

            //Se debe verificar que las imagenes se encuentren previamente
            if ($this->findFavoriteImage($registerImageBD)) { 
                
                $tempOwner->setGalleryForm($registerImageBD);
                $rpTempOwner->save($tempOwner);
                return new ResponseCommandBus(201,'OK'); 
                
            } else {
                return new ResponseCommandBus(401,'Unauthorized ');        
            }

        } else {
            return new ResponseCommandBus(401,'Unauthorized ');
        }
    }

    /**
    * La siguente función se encarga de verificar que la imagen a eliminar
    * como favoritos se encuentre almacenada previamente y retorna verdadero
    * si las imagenes se encuentran y se elimino
    *
    * @author Freddy Contreras <freddycontreras3@gmail.com>
    * @param array $registerImageBD
    * @return boolean
    * @version 08/07/2015
    */
    private function findFavoriteImage(&$registerImageBD)
    {
        $response = false;

        foreach ($registerImageBD['favorites'] as $key => $currentFavorite) {            
            
            if ($currentFavorite['path'] == $this->command->getPath() and 
                $currentFavorite['subGallery'] == $this->command->getSubGallery()) {
                $response = true;
                array_splice($registerImageBD['favorites'], $key, 1);
                //unset($registerImageBD['favorites'][$key]); 
                break;
            }            
        }

        return $response;     
    }
}