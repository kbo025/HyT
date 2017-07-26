<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Navicu\Core\Domain\Repository\ReservationCanceledRepository;

/**
 * La clase se declaran los metodos y funciones que implementan
 * el repositorio de la entidad RoomImagesGallery
 *
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 * @version 26/05/15
 */

class DbReservationCanceledRepository extends EntityRepository implements
    ReservationCanceledRepository
{

}