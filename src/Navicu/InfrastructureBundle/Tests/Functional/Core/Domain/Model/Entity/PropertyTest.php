<?php

namespace Navicu\InfrastructureBundle\Tests\Functional\Core\Domain\Model;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Navicu\Core\Domain\Model\Entity\Property;
use Navicu\Core\Domain\Model\ValueObject\Coordinate;
use Navicu\Core\Domain\Model\ValueObject\Url;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Model\ValueObject\Password;
use Navicu\Core\Domain\Model\ValueObject\Phone;

/**
 *
 * La siguente clase se encarga de ejecutar las pruebas funcionales
 * de la entidad Property (establecimiento)
 * 
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 * @version 01/06/2015
 */
class PropertyTest extends KernelTestCase
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
     * Metodo comprueba que los establecimientos tenga un mínimo
     * de imagenes favoritas
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     * @version 01/06/2015
     */
	public function testMinFavoriteImages() 
    {
		echo "------------------------------\n";
		echo "* Clase: Property\n";
		echo "* Prueba Funcional: Mínimo 8 Imagenes Favoritas del establecimiento\n";
		
		$properties = $this->em->getRepository('NavicuDomain:Property')->findAll();
		
		foreach ($properties as $currentProperty) {
			$countFavorite = count($currentProperty->getPropertyFavoriteImages());

			$this->assertTrue($countFavorite >= 8, 
				"\n- El establecimiento ".$currentProperty->getName()." tiene menos de 8 imagenes favoritas\n");
		}
	}

    /**
     * Método comprueba los establecimientos tenga una imagen de perfil
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     * @version 01/06/2015
     */
    public function testIsNullImageProfile() 
    {
        echo "------------------------------\n";
        echo "* Clase: Property\n";
        echo "* Prueba Funcional: Establecimiento con una imagen de perfil\n";
        
        $properties = $this->em->getRepository('NavicuDomain:Property')->findAll();
        
        foreach ($properties as $currentProperty) {
            
            $countProfileImage = count($currentProperty->getProfileImage());

            $this->assertTrue($countProfileImage == 1, 
                "\n- El establecimiento con ID:".$currentProperty->getId()." no tiene imagen de perfil\n");
        }
    }

    /**
     * Método comprueba los establecimientos tenga al menos una galería de imagenes
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     * @version 01/06/2015
     */
    public function testMinPropertyGallery()
    {
        echo "------------------------------\n";
        echo "* Clase: Property\n";
        echo "* Prueba Funcional: Establecimiento con mínimo una galería de imagenes\n";

        $properties = $this->em->getRepository('NavicuDomain:Property')->findAll();
        
        foreach ($properties as $currentProperty) {
            
            $countGallery = count($currentProperty->getPropertyGallery());

            $this->assertTrue($countGallery >= 1, 
                "\n- El establecimiento con ID:".$currentProperty->getId()." no tiene galería de imagenes\n");
        }
    }

    /**
     * Prueba el funcionamiento del repositorio cuando encuentra dos slug iguales
     *
     * @author Gabriel Camacho
     * @author Currently Working: Gabriel Camacho
     * @version 06-07-15
     */
    public function testResolutionSlug() 
    {
        echo "------------------------------\n";
        echo "* Clase: Property\n";
        echo "* Prueba Funcional: Tratamiento de Slug en caso de coliciones\n";
        
        $properties = $this
            ->em
            ->getRepository('NavicuDomain:Property');

        $property = new Property();
        $property->setName('Property1');
        $properties->validateSlug($property);
        $this->assertEquals(
            $property->getSlug(),
            'property1-2'
        );
       
    }

    /**
     * Prueba el funcionamiento del repositorio
     *
     * @author Gabriel Camacho
     * @author Currently Working: Gabriel Camacho
     * @version 06-07-15
     */
    public function testGeneratePublicId()
    {
        echo "------------------------------\n";
        echo "* Clase: Property\n";
        echo "* Prueba Funcional: Generacion de Id Publica\n";
        
        $properties = $this
            ->em
            ->getRepository('NavicuDomain:Property');
        $val = $this
            ->em
            ->getRepository('NavicuDomain:Location')
            ->findOneBy(array('title'=>'Valencia'));
        $property = new Property();
        $property->setName('Property1');
        $property->setLocation($val);
        $properties->validatePublicId($property);
        $this->assertNotEmpty(
            $property->getPublicId()
        ); 
       
    }
}
