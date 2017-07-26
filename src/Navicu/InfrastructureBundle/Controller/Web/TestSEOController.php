<?php

namespace Navicu\InfrastructureBundle\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;


/**
 * El siguiente controlador se encarga de ser pruebas de los correos
 * del sistema, es el acceso a las vista de los correos
 *
 * Class TestEmailController
 * @package Navicu\InfrastructureBundle\Controller\Web
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 08/01/2016
 */
class TestSEOController extends Controller
{
    /**
     * La siguiente función carga la plantilla de los correos del sistema
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $email
     * @param $send
     * @return Response
     */
    public function SEOPropertiesAction()
    {
        $properties = $this->get('doctrine.orm.entity_manager')->getRepository('NavicuDomain:Property')->findAll();

        $response = [];

        global $kernel;

        $kernel = $this->get('kernel');
        $path = $kernel->locateResource('@NavicuInfrastructureBundle/Resources/translations/ES/SEO/seo.es.yml');

        $value = Yaml::parse(file_get_contents($path));

        $i = 1;
        foreach ($properties as $currentProperty) {
            if (!array_key_exists($currentProperty->getSlug(),$value['SEO']['property'])) {
                echo $i . '.- ' . $currentProperty->getSlug() . '<br>';
                $i++;
            }
        }

        return new Response();
    }

	public function coordinatesAction()
	{
	}
}

