<?php

namespace Navicu\InfrastructureBundle\Tests\Functional\Core\Domain\Model;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 *
 * La siguente clase se encarga de ejecutar las pruebas funcionales
 * de la entidad PropertyImagesGallery (galería de imagenes del establecimiento)
 * 
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 * @version 01/06/2015
 */
class PropertyImagesGalleryTest extends KernelTestCase
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
     * Método comprueba que las galería de imagenes de la habitación esta
     * asociada a una galeria del establecimiento
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     * @version 01/06/2015
     */
    public function testIsNullPropertyGallery() 
    {

        echo "------------------------------\n";
        echo "* Clase: PropertyImagesGallery\n";
        echo "* Prueba Funcional: Esta asociada a una galeria del establecimiento\n";
        
        $propertiesImagesGallery = $this->em->getRepository('NavicuDomain:PropertyImagesGallery')->findAll();
        
        foreach ($propertiesImagesGallery as $currentGallery) {
            $propertyGallery = count($currentGallery->getPropertyGallery());

            $this->assertTrue($propertyGallery == 1, 
                "\n- La imagen del establecimiento con ID:".$currentGallery->getId()." no tiene galería asociada\n");
        }
    }

    /**
     * Método comprueba que las galería de imagenes del establecimiento esta
     * asociada a una imagen
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     * @version 01/06/2015
     */
    public function testIsNullImage() 
    {

        echo "------------------------------\n";
        echo "* Clase: PropertyImagesGallery\n";
        echo "* Prueba Funcional: Tiene una imagen asociada\n";
        
        $propertiesImagesGallery = $this->em->getRepository('NavicuDomain:PropertyImagesGallery')->findAll();
        
        foreach ($propertiesImagesGallery as $currentGallery) {
            $image = count($currentGallery->getImage());

            $this->assertTrue($image == 1, 
                "\n- La imagen del establecimiento con ID:".$currentGallery->getId()." no tiene imagen asociada\n");
        }
    }
}
