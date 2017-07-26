<?php

namespace Navicu\InfrastructureBundle\Tests\Functional\Core\Domain\Model;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 *
 * La siguente clase se encarga de ejecutar las pruebas funcionales
 * de la entidad PropertyGallery (galería del establecimiento)
 * 
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 * @version 01/06/2015
 */
class PropertyGalleryTest extends KernelTestCase
{
     /**
     * La función se encarga declarar el entity manager
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;
    }


    /**
     * Metodo comprueba que las galerias de imagenes pertenezcan a un establecimiento
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     * @version 01/06/2015
     */
    public function testIsNullProperty() 
    {
        echo "------------------------------\n";
        echo "* Clase: PropertyGallery\n";
        echo "* Prueba Unitaria: La galería pertenece a un establecimiento\n";
        
        $propertyGalleries = $this->em->getRepository('NavicuDomain:PropertyGallery')->findAll();
        
        foreach ($propertyGalleries as $currentGallery) {
            
            $property = count($currentGallery->getProperty());           
            
            $this->assertTrue($property == 1, 
                "\n- La Galería de imagen con ID:".$currentGallery->getId()." no esta asociada a un establecimiento\n");
        }
    }

    /**
     * Método comprueba que las galerias de imagenes tenga al menos una imagen
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     * @version 01/06/2015
     */
    public function testIsNullImages() 
    {
        echo "------------------------------\n";
        echo "* Clase: PropertyGallery\n";
        echo "* Prueba Unitaria: La galería contenga al menos una imagen\n";
        
        $propertyGalleries = $this->em->getRepository('NavicuDomain:PropertyGallery')->findAll();
        
        foreach ($propertyGalleries as $currentGallery) {
            
            $countImages = count($currentGallery->getImagesGallery());

            $this->assertTrue($countImages >= 1, 
                "\n- La Galería de imagen del establecimiento con ID:".$currentGallery->getId()." no tiene asociada ninguna imagen\n");
        }
    }

    /**
     * Método comprueba que las galerias de imagenes tenga pertenezca a una categoría (Tipo de gallería)
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     * @version 01/06/2015
     */
    public function testIsNullCategory() 
    {
        echo "------------------------------\n";
        echo "* Clase: PropertyGallery\n";
        echo "* Prueba Unitaria: La galería pertenece a un categoría (Tipo de gallería)\n";
        
        $propertyGalleries = $this->em->getRepository('NavicuDomain:PropertyGallery')->findAll();
        
        foreach ($propertyGalleries as $currentGallery) {
            
            $type = count($currentGallery->getType());
            
            $this->assertTrue($type == 1, 
                "\n- La Galería de imagen con ID:".$currentGallery->getId()." no esta asociada a un \"Tipo de Galleria\" \n");
        }
    }
}
