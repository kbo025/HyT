<?php

namespace Navicu\Core\Domain\Repository;

use Navicu\Core\Domain\Model\Entity\DeniedReservation;

interface DeniedReservationRepository
{
    /**
     * almacena en BD toda la informacion referente a una reserva
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @param  Reservation
     */
    public function save(DeniedReservation $reservation);

    /**
     * Funcion para persistir objetos temporalmente
     * @param $reservation
     */
    public function persistObject($reservation);

    /**
     * Funcion para guardar todos los objetos que existan en la cache temporal
     */
    public function flushObject();
} 