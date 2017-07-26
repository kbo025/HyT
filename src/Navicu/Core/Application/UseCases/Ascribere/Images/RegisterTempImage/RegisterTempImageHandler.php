<?php

namespace Navicu\Core\Application\UseCases\Ascribere\Images\RegisterTempImage;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Model\Entity\Document;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Model\ValueObject\Slug;

/**
* Clase para ejecutar el caso de uso RegisterTempServices
* @author Freddy Contreras <freddycontreras3@gmail.com>
* @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
* @version 08/06/2015
*/

class RegisterTempImageHandler implements Handler
{
    private $command;

    private $rf;

    /**
     * Ejecuta el caso de uso 'Registrar una imagen en espacio Temporal'
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

            //Se obtiene los datos de las imagenes previamente cargada
            $registerImagesBD = $tempOwner->getGalleryForm();
            //Se obtiene los datos del establecimiento
            $registerProperty = $tempOwner->getPropertyForm();

            $repositoryDocument = $rf->get('Document');
            
            //Se crea el nombre el archivo a almecenar
            $nameFile =
                'navicu-reserva-'.
                Slug::generateSlug(trim($registerProperty['name'],' ')).'-';

            $folder1 = Slug::generateSlug($command->getSlug());
            $folder2 = Slug::generateSlug($command->getGallery());
            $folder3 = Slug::generateSlug($command->getSubGallery());

            //Se crea el nombre de la ruta del archivo
            $path = 'property/'.
                $folder1.'/'.
                $folder2.'/'.
                $folder3.'/';


            //Se crea el documento
            $document = new Document();
            $document->setFile($command->getImage());
            $document->setName($command->getName());
            $document->setFileName($path);
            $document->upload('image',$path, $nameFile);

            $this->setDataFormImages(
                $registerImagesBD,
                $document,
                $command->getIdSubGallery(),
                $command->getGallery(), 
                $command->getSubGallery());

            //Se actualiza los datos de la galería
            $tempOwner->setGalleryForm($registerImagesBD);
            //Se persiste en la BD los datos de la nueva imagen
            $rpTempOwner->save($tempOwner);

            //Almacenando el nombre del path de la imagen
            $command->setPathImage($document->getFileName());

            $responseArray = array();
            $responseArray['name'] = $document->getName();
            $responseArray['path'] = $document->getFileName();
            
            return new ResponseCommandBus(201,'OK',$responseArray);
                
        } else {
            return new ResponseCommandBus(401,'Unauthorized ');  
        }

    }

    /**
    * La función se encarga en almacenar la información de una imagen
    * en la BD dependiendo del usuario 
    * 
    * @author Freddy Contreras <freddycontreras3@gmail.com>
    * @param Array $registerImageBD
    * @param Document $document
    * @param integer $idGallery
    * @param string $gallery
    * @param string $subGallery
    * @return Void
    * @version 13/07/2015
    */
    private function setDataFormImages(&$registerImagesBD, $document, $idGallery, $gallery, $subGallery)
    {
        $imageRegister['name'] = $document->getName();
        $imageRegister['path'] = $document->getFileName();

        //Si no existe ninguna imagen
        if (empty($registerImagesBD)) {

            $registerImagesBD = array();
            $registerImagesBD[$gallery] = array();
            $newGallery = array();
            $newGallery['idSubGallery'] = $idGallery;
            $newGallery['subGallery'] = $subGallery;
            $newGallery['images'] = array();
            array_push($newGallery['images'], $imageRegister);
            array_push($registerImagesBD[$gallery], $newGallery);

        } else {
            
            //Si existe la galería
            if (!isset($registerImagesBD[$gallery])) {
                
                $registerImagesBD[$gallery] = array();
                $newGallery = array();
                $newGallery['idSubGallery'] = $idGallery;
                $newGallery['subGallery'] = $subGallery;
                $newGallery['images'] = array();
                array_push($newGallery['images'], $imageRegister);
                array_push($registerImagesBD[$gallery], $newGallery);

            } else {
                
                $foundGallery = false;
                //buscandeo si la Galería existe
                foreach ($registerImagesBD[$gallery] as &$currentGallery) {
                    
                    if ($currentGallery['idSubGallery'] == $idGallery and 
                        $currentGallery['subGallery'] == $subGallery) {
                        $foundGallery = true;
                        $currentGallery;
                        break;
                    }
                }

                //Si existe la galería
                if ($foundGallery) {
                    if (!isset($currentGallery['images'])) 
                        $currentGallery['images'] = array();

                    array_push($currentGallery['images'], $imageRegister);

                } else {
                    
                    $newGallery = array();
                    $newGallery['idSubGallery'] = $idGallery;
                    $newGallery['subGallery'] = $subGallery;
                    $newGallery['images'] = array();
                    array_push($newGallery['images'], $imageRegister);
                    array_push($registerImagesBD[$gallery], $newGallery);
                }
            }
        }
    }
}