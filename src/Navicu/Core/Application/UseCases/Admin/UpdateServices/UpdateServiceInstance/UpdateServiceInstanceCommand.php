<?php
/**
 * Created by PhpStorm.
 * User: developer2
 * Date: 21/12/15
 * Time: 10:29 AM
 */

namespace Navicu\Core\Application\UseCases\Admin\UpdateServices\UpdateServiceInstance;


use Navicu\Core\Application\Contract\Command;

class UpdateServiceInstanceCommand implements Command
{
    /**
     *  slug del establecimiento
     * @var String $slug
     */
    private $slug;

    /**
     *  identificador del restaurant del establecimiento que se desea eliminar
     *  @var integer id
     */
    private $id;

    /**
     *  tipo de instancia de servicio que se pretende eliminar (restaurant, bar, Salon)
     *  @var integer $type
     */
    private $idType;

    /**
     * contiene los datos que se van a actualizar o a agregar en la instancia
     * @var array $data
     */
    private $data;

    /**
     *  constructor
     *
     * @var array $data
     * @var integer $type
     */
    public function __construct($data)
    {
        foreach($data as $index => $att)
            $this->set($index,$att);
    }

    /**
     *  metodo getRequest devuelve un array con los parametros del command
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 05-05-2015
     * @return  Array
     */
    public function getRequest()
    {
        return [
            'slug' => $this->slug,
            'id' => $this->id,
            'idType' => $this->idType,
            'data' => $this->data,
        ];
    }

    /**
     * get
     *
     * @param $att
     * @return mixed
     */
    public function get($att)
    {
        return ( isset($this->$att) ? $this->$att : null );
    }

    /**
     * set
     *
     * @param $att
     * @param $val
     * @return null
     */
    public function set($att,$val)
    {
        $this->$att = $val;
    }
}