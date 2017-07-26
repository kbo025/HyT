<?php
/**
 * Created by PhpStorm.
 * User: developer2
 * Date: 22/07/16
 * Time: 05:03 PM
 */

namespace Navicu\InfrastructureBundle\Repositories;

use Navicu\Core\Domain\Repository\DeniedReservationRepository;
use Navicu\Core\Domain\Model\Entity\DeniedReservation;
use Doctrine\ORM\EntityRepository;

class DbDeniedReservationRepository extends EntityRepository implements DeniedReservationRepository 
{

    /**
     * almacena en BD toda la informacion referente a una reserva
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @param  Reservation
     */
    public function save(DeniedReservation $reservation)
    {
        $this->getEntityManager()->persist($reservation);
        $this->getEntityManager()->flush();
    }

    /**
     * Funcion para persistir objetos temporalmente
     * @param Reservation $reservation
     */
    public function persistObject($reservation)
    {
        $this->getEntityManager()->persist($reservation);
    }

    /**
     * Funcion para guardar todos los objetos que existan en la cache temporal
     */
    public function flushObject()
    {
        $this->getEntityManager()->flush();
    }
}