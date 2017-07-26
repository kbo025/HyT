<?php
namespace Navicu\InfrastructureBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * La siguiente clase se encarga de recibir peticiones génericas
 * respecto a modulos que trabajen con subdominios
 *
 * Class SubdomainController
 * @package Navicu\InfrastructureBundle\Controller
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 24/10/2016
 */
class SubdomainController extends Controller
{
    /**
     * Función donde se carga el homepage dado un subdominio
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 20/101/2016
     */
    public function indexAction()
    {
        $subdomain = $this->get('request')->get('subdomain');
        return $this->redirect($this->generateUrl('navicu_aavv_home',['subdomain' => $subdomain]));
    }
}