<?php
namespace Navicu\Core\Application\UseCases\Extranet\GetReservationsForProperty;

use Navicu\Core\Application\Contract\Command;

/**
 * Caso de uso para devolver una lista de las reservas para un establecimiento
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @version 15-03-2016
 */
class GetReservationsForPropertyCommand implements Command
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
     *  metodo getRequest devuelve un array con los parametros del command
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 05-05-2015
     * @return  Array
     */
    public function getRequest()
    {
        return [
            'id' => $this->id,
            'checkIn' => $this->checkIn,
            'checkOut' => $this->checkOut,
            'slug' => $this->slug,
            'page' => $this->page,
            'status' => $this->status,
            'date' => $this->date
        ];
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