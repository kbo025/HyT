<?php
// src/Facebook/Bundle/Twig/FacebookExtension.php
namespace Navicu\InfrastructureBundle\Resources\Services;

use Navicu\Core\Application\UseCases\Web\getDestinationsFooter\getDestinationsFooterCommand;
use Navicu\Core\Application\UseCases\Web\ForeignExchange\GetListCurrency\GetListCurrencyCommand;
use Navicu\Core\Domain\Adapter\CoreSession;

/**
 * Extension de Twig, donde se puede crear funciones y filtros de twig
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * Class TwigExtension
 * @package Navicu\InfrastructureBundle\Resources\Services
 */
class TwigExtension extends \Twig_Extension
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions() {
        return array(
            'hasAccessAdmin' => new \Twig_Function_Method($this, 'hasAccessAdmin'),
            'destinationsFooter' => new \Twig_Function_Method($this, 'destinationsFooter'),
            'getListCurrency' => new \Twig_Function_Method($this, 'getListCurrency'),
            'hasPermission' => new \Twig_Function_Method($this, 'hasPermission'),
            'pathSubdomain' => new \Twig_Function_Method($this, 'pathSubdomain')
        );
    }

    /**
     * Verifica si el usuario tiene acceso a los modulos de admin
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $module
     * @param null $permission
     * @return bool
     */
    public function hasAccessAdmin($module, $permission = null)
    {
        $nvcProfile = $this->container->get('security.context')->getToken()->getUser()->getNvcProfile();
        if ($nvcProfile)
            return $nvcProfile->havePermissons($module, $permission);
        else
            return false;
    }

    public function hasPermission($module, $permission = null)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if ($user->hasRole('ROLE_ADMIN'))
            return true;
        else
            return $user->hasAccess($module, $permission);
    }

    public function getName()
    {
        return 'app_extension';
    }

    /**
     * Función es usada para devolver un conjunto de destinos para
     * el footer.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     * @return Array
     */
    public function destinationsFooter()
    {

        $command = new getDestinationsFooterCommand();
        $response = $this->container->get('CommandBus')->execute($command);

        return $response->getData();
    }

    /**
     * Funcion encargada de devolver a fronEnd el listado de monedas 
     * @return mixed
     */
    public function getListCurrency() {
        $command = new GetListCurrencyCommand();
        $response = $this->container->get('CommandBus')->execute($command);

        return $response->getData();
    }

    public function pathSubdomain($route, $data = [])
    {
        $data['subdomain'] = $this->container->get('request')->get('subdomain');
        if (strcmp($route, 'navicu_aavv_resetting') == 0)
            $data['subdomain'] = 'www';
        return $this->container->get('router')->generate($route,$data);
    }
}