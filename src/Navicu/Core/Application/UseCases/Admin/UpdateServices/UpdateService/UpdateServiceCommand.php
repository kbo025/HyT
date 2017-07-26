<?php
/**
 * Created by PhpStorm.
 * User: developer2
 * Date: 09/12/15
 * Time: 03:43 PM
 */

namespace Navicu\Core\Application\UseCases\Admin\UpdateServices\UpdateService;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Domain\Adapter\EntityValidationException;

class UpdateServiceCommand implements  Command{

    /**
     * slug del hotel
     *
     * @var string $slug
     */
    private $slug;

    /**
     * id del servicio
     *
     * @var integer $id
     */
    private $id;

    /**
     * nombre del servicio
     *
     * @var string $name
     */
    private $name;

    /**
     * id del tipo de srevicio
     *
     * @var integer $type
     */
    private $idType;

    /**
     * si se pretende activar o desactivar el servicio
     *
     * @var boolean $status
     */
    private $status;

    /**
     * si el servicio es gratuito
     *
     * @var boolean $free
     */
    private $free;

    /**
     * cantidad de instancias del servicio
     *
     * @var integer $quantity
     */
    private $quantity;

    /**
     * horario de prestacion del servicio
     *
     * @var array $schedule
     */
    private $schedule;

    /**
     * constructor
     *
     * @param array $data
     */
    public function __construct($data=null)
    {
        foreach($data as $index => $att)
            $this->set($index,$att);
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

    /**
     * @return array
     */
    public function getRequest()
    {
        return [
            'slug' => $this->slug,
            'id' => $this->id,
            'name' => $this->name,
            'idType' => $this->idType,
            'status' => $this->status,
            'free' => $this->free,
            'quantity' => $this->quantity,
            'schedule' => $this->schedule
        ];
    }
} 