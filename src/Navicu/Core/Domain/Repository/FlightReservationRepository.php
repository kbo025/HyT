<?php

namespace Navicu\Core\Domain\Repository;

/**
 * Interfaz de FlightReservationRepository
 * @author Alejandro Conde <adcs2008@gmail.com>
 * @version 03/11/2016
 */

interface FlightReservationRepository
{
	public function findOneByArray($array);

	public function save($obj);

	public function delete($obj);
}