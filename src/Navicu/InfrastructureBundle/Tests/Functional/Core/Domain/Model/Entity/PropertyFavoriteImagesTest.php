<?php

namespace Navicu\InfrastructureBundle\Tests\Functional\Core\Domain\Model;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 *
 * La siguente clase se encarga de ejecutar las pruebas funcionales de la entidad
 * PropertyFavoritresImages (Images favoritas del establecimiento)
 * 
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 * @version 01/06/2015
 */
class PropertyFavoriteImagesTest extends KernelTestCase
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
     * Metodo comprueba que una imagen favorita del establecimiento
     * tenga asociado una imagen
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     * @version 01/06/2015
     */
	public function testIsNullImage()
	{
		echo "------------------------------\n";
		echo "* Clase: PropertyFavoriteImagesTest\n";
		echo "* Prueba Funcional: Tiene una imagen asociada\n";

		$propertyFavoriteImages = $this->em
			->getRepository('NavicuDomain:PropertyFavoriteImages')
				->findAll();

		foreach ($propertyFavoriteImages as $currentFavorite) {
			
			$countImage = count($currentFavorite->getImage());

			$this->assertTrue($countImage == 1, 
				"\n- La imagen Favorita con ID = ".$currentFavorite->getId()." tiene ".$countImage." imagenes asociadas\n");
		}
	}

	/**
     * Metodo comprueba que una imagen favorita del establecimiento
     * tenga asociado solamente un establecimiento
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     * @version 01/06/2015
     */
	public function testIsNullProperty()
	{
		echo "------------------------------\n";
		echo "* Clase: PropertyFavoriteImages\n";
		echo "* Prueba Funcional: Tiene solo un establecimiento asociado\n";

		$propertyFavoriteImages = $this->em
			->getRepository('NavicuDomain:PropertyFavoriteImages')
				->findAll();

		foreach ($propertyFavoriteImages as $currentFavorite) {
			
			$countProperty = count($currentFavorite->getProperty());

			$this->assertTrue($countProperty == 1, 
				"\n- La imagen Favorita con ID = ".$currentFavorite->getId()." tiene ".$countProperty." establecimientos asociados\n");
		}
	}
}
