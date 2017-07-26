<?php

namespace Navicu\Core\Application\UseCases\PropertyInventory\MassLoad\SetDataMassLoad;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Services\SecurityACL;

/**
 * Comando 'Comando que procesa los datos de la carga másiva del inventario'
 * ontreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 * @version 13/10/2015
 */

class SetDataMassLoadCommand implements Command
{
    /**
     * Representa el slug del establecimiento a consultar
     */
    private $slug;

    /**
     * @var $session Manejo de los recurso de session por medio de profileOwner.
     */
    private $session;

    /**
     * La siguiente  función contiene un json con los datos de la carga másiva
     * @var json
     */
    private $data;

    /**
     * @var la variable contiene los datos del usuario (ownerProfile)
     */
    private $userSession;


    private $apiRequest;

    /**
     *   constructor de la clase
     *   @param json $data
     */
    public function __construct($slug, $session, $data,$apiRequest = false)
    {
        $this->slug = $slug;
        $this->session = SecurityACL::isSlugOwner($slug, $session);
        $this->data = $data;
        $this->userSession = $session;
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
            'session' => $this->session,
            'data' => $this->data,
            'apiRequest' => $this->apiRequest
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

    public function getUserSession()
    {
        return $this->userSession;
    }

    public function isApiRequest()
    {
        return $this->apiRequest;
    }
}