<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 11/10/16
 * Time: 09:34 AM
 */

namespace Navicu\Core\Application\UseCases\AAVV\Reservation\GetReservationDetail;


use Navicu\Core\Application\Contract\Command;

/**
 * Class GetDetailReservationCommand Encargada de construir
 * el objte para detallar la informacion de la reserva
 *
 * @package Navicu\Core\Application\UseCases\AAVV\Reservation\GetReservationDetail
 */
class GetReservationDetailCommand implements Command
{
    protected $idReservation;

    public function __construct($id)
    {
        $this->idReservation = (!is_null($id)) ? $id : null;
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
            "id_reservation" => $this->idReservation,
        ];
    }
}