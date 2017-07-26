<?php

namespace Navicu\Core\Application\UseCases\Ascribere\Images\RegisterTempFavoritesImages;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

/**
* Clase para ejecutar el caso de uso RegisterTempFavoritesImages
* @author Freddy Contreras <freddycontreras3@gmail.com>
* @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
* @version 07/07/2015
*/

class RegisterTempFavoritesImagesHandler implements Handler
{
    private $command;

    private $rf;

    /**
     * Ejecuta el caso de uso 'Registrar las imagenes favoritas en espacio Temporal'
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
            // cargadas en el sistema
            if ($this->findFavoritesImages($registerImageBD)) { 
                
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
    * La siguente función se encarga de verificar que las imagenes a almacenar
    * como favoritos se encuentre almacenada previamente y retorna verdadero
    * si todas las imagenes se encuentran previamente almecenada.
    *
    * @author Freddy Contreras <freddycontreras3@gmail.com>
    * @param array $registerImageBD
    * @return boolean
    * @version 07/07/2015
    */
    private function findFavoritesImages(&$registerImageBD)
    {
        $response = true;
        
        $registerImageBD['favorites'] = array();

        //Se recorren todos los valores de las imagenes favoritas
        foreach ($this->command->getFavoritesImages() as $currentImages) {
            
            //Se toma el iterador de un el array de las imagenes previamente cargadas
            $it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($registerImageBD)); 

            $flag = false;

            foreach($it AS $key => $element) { 
                if($element == $currentImages['path']) {
                    $webDirectory = $_SERVER['DOCUMENT_ROOT'].'/uploads/images/';

                    //Verificando que existan los distintos archivos referente a la imagen
                    if (file_exists($webDirectory.'images_original/'.$currentImages['path']) and 
                        file_exists($webDirectory.'images_md/'.$currentImages['path']) and 
                        file_exists($webDirectory.'images_sm/'.$currentImages['path']) and 
                        file_exists($webDirectory.'images_xs/'.$currentImages['path'])) {
                        
                        $flag = true;                    
                        array_push($registerImageBD['favorites'], $currentImages);
                    }
                    
                    break;
                } 
            }
            //Se retorna falso si una de las imagenes no se encuentra 
            // como imagen previamente cargada
            if (!$flag) {
                $response = false;
                break;
            }
        }

        return $response;     
    }
}