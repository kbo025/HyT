<?php
/**
 * Created by PhpStorm.
 * User: developer2
 * Date: 28/03/16
 * Time: 02:46 PM
 */

namespace Navicu\Core\Application\UseCases\Client\GetReservationsForClient;


use Navicu\Core\Application\Contract\Command;

class GetReservationsForClientCommand implements Command
{

    /**
     * pagina  a la que se desea acceder
     */
    private $page;

    /**
     * Slug del hotel
     */
    private $slug;

    /**
     * fecha de check in para la busqueda
     */
    private $checkIn;

    /**
     * fecha de check out para la busqueda
     */
    private $checkOut;

    /**
     * id de la reservación para la busqueda
     */
    private $id;

    /**
     * filtro por estado de la reserva
     */
    private $status;

    /**
     * filtro por fecha de creacion de la reserva
     */
    private $date;

    /**
     * tipo de listado que se va a traer de base de datos
     * 1. listas y filtros normales
     * 2. lista de reservas con checkin mayor al día actual
     */
    private $type;
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
            'checkIn' => $this->checkIn,
            'checkOut' => $this->checkOut,
            'date' => $this->date,
            'id' => $this->id,
            'page' => $this->page,
            'slug' => $this->slug,
            'status' => $this->status,
            'idClient' => $this->idClient,
            'type' => $this->type
        ];
    }

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
        foreach($data as $key => $val) {
            $this->set($key,$val);
        }
    }

    /**
     * devueleve el valor del atributo pasado por parametro, null si no existe
     *
     * @param string $att
     * @return mixed || null
     */
    public function get($att)
    {
        if(isset($this->$att))
            return $this->$att;
        return null;
    }

    /**
     * modifica el valor del atributo pasado por parametro sustituyendolo con $val
     *
     * @param string $att
     * @param mixed $val
     * @return Command;
     */
    public function set($att,$val)
    {
        if(!empty($val))
            $this->$att = $val;
        return $this;
    }
}