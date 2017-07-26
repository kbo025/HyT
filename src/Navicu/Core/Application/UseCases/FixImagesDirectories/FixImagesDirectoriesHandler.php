<?php
/**
 * Created by PhpStorm.
 * User: developer10
 * Date: 05/04/17
 * Time: 10:08 AM
 */

namespace Navicu\Core\Application\UseCases\FixImagesDirectories;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

class FixImagesDirectoriesHandler implements Handler
{
    /**
     *  Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $propertyRepository = $rf->get('Property');

        $properties = $propertyRepository->getInconsistentFolders();

        $fixed = 0;

        foreach ($properties as $property) {
            $propertyInstance = $propertyRepository->find($property['id']);

            if ($propertyInstance) {
                $propertyInstance->renameFoldersFromConsole($command->get('basePath'),$property['oldslug']);
                $fixed++;
            }

            //Crear los directorios errados para las pruebas
//            $webPath = $_SERVER['DOCUMENT_ROOT'].'/uploads/images/';
//            $documentPath = $_SERVER['DOCUMENT_ROOT'].'/uploads/documents/';
//
//            $basePath = '/home/developer10/Repos/navicu/web/uploads/images/';
//
//            system("mkdir ".$basePath.'images_original/property/'.$property['oldslug']);
//
//            system("mkdir ".$basePath.'images_md/property/'.$property['oldslug']);
//
//            system("mkdir ".$basePath.'images_sm/property/'.$property['oldslug']);
//
//            system("mkdir ".$basePath.'images_xs/property/'.$property['oldslug']);
        }

        $response['fixed'] = $fixed;

        return new ResponseCommandBus(200, 'ok',$response);
    }
}