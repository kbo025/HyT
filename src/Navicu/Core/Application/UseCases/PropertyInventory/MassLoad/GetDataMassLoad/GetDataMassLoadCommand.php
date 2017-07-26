<?php

namespace Navicu\Core\Application\UseCases\PropertyInventory\MassLoad\GetDataMassLoad;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Services\SecurityACL;

/**
 * Comando 'Comando que permite obtener los datos de un establecimiento, en la carga másiva'
 * ontreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 * @version 07/10/2015
 */

class GetDataMassLoadCommand implements Command
{
    /**
     * Representa el slug del establecimiento a consultar
     */
    protected $slug;

    /**
     * @var $session Manejo de los recurso de session por medio de profileOwner.
     */
    private $session;

    private $apiRequest;

    /**
     *   constructor de la clase
     *   @param string $slug
     */
    public function __construct($slug = null, $session, $apiRequest = false)
    {   $this->slug = $slug;
        $this->session = SecurityACL::isSlugOwner($slug, $session);
        $this->apiRequest = $apiRequest;
    }

    /**
     * La función retorna todos los atributos de la clase en un arreglo
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @return Array
     */
    public function getRequest()
    {
        return array(
            'slug' => $this->slug,
            'session' => $this->session
        );
    }

    /**
     *  Método get devuelve el atributo del comando que se pasa por parametro
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param String $att
     * @version 13/09/2015
     * @return  mixed
     */
    public function get($att)
    {
        if(isset($this->$att))
            return $this->$att;
        else
            throw new \Exception('The class '.get_class($this).' not contains the attribute '.$att);
    }

    /**
     *  Método actualiza el atributo del comando dado un string que representa el nombre atributo
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param String $att
     * @param mixed $value
     * @version 13/09/2015
     */
    public function set($att, $value)
    {
        if(isset($this->$att))
            $this->$att = $value;
        else
            throw new \Exception('The class '.get_class($this).' not contains the attribute '.$att);
    }

    public function isApiRequest()
    {
        return $this->apiRequest;
    }
}