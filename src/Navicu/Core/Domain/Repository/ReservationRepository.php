<?php

namespace Navicu\Core\Domain\Repository;


use Navicu\Core\Domain\Model\Entity\Reservation;
/**
 * Interfaz de ReservationRepository
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 * @version 20/08/2015
 */
interface ReservationRepository
{
    /**
     * almacena en BD toda la informacion referente a una reserva
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @param  Reservation
     */
    public function save($reservation);

    /**
     * Funcion para persistir objetos temporalmente
     * @param Reservation $reservation
     */
    public function persistObject($reservation);

    /**
     * Funcion para guardar todos los objetos que existan en la cache temporal
     */
    public function flushObject();

    /**
     * Obtiene todas las reservas
     * @author Jose Agraz <jaagraz@navicu.com>
     * @version 22/01/2016
     * @param
     */
    public function findAllReservationToAdmin($array);

    /**
     *  encuentra todas las reservaciones de un hotels
     *
     * @param $filters
     * @return array
     */
    public function findReservationsForProperty($filters = []);

    /**
     *  encuentra todas las reservaciones de un hotels
     *
     * @param $filters
     * @return array
     */
    public function findReservationsForClient($filters = []);

    /**
     * Busca una reserva segun el publicId
     * @author Jose Agraz <jaagraz@navicu.com>
     * @version 15/02/2016
     * @param
     */
    public function findByPublicId($array);
}


