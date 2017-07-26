<?php
//Uso de la clase compiler
namespace Navicu\InfrastructureBundle\DependencyInjection\Compiler;

use Symfony\Component\Finder\Finder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\Config\Resource\DirectoryResource;

/**
 * Clase ValidatorRouting
 *
 * Se define una clase y una funcion necesarios para definir la ruta de los archivos de
 * validación de las entidades.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class ValidatorRouting implements CompilerPassInterface
{
    /**
     * Esta función es usada para definir la ruta de los archivos de
     * validación de las entidades.
     * 
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     * 
     * @param \ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $validatorBuilder = $container->getDefinition('validator.builder');
        $validatorFiles = array();
        $finder = new Finder();

        foreach ($finder->files()->in(__DIR__ . '/../../../Core/Application/Validator/Group') as $file) {
            $validatorFiles[] = $file->getRealPath();
        }

        $validatorBuilder->addMethodCall('addYamlMappings', array($validatorFiles));

        // Se agrega todo los archivos de validación existentes en la carpeta Validator/conifg
        $container->addResource(new DirectoryResource(__DIR__ . '/../../../Core/Application/Validator/Group/'));
    }
}
