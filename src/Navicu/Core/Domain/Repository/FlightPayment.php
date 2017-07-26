<?php

namespace Navicu\Core\Domain\Repository;

/**
 * Interfaz de FlightPaymentRepository
 * @author Alejandro Conde <adcs2008@gmail.com>
 * @version 21/06/2017
 */

interface FlightPaymentRepository
{
	public function findOneByArray($array);

	public function save($obj);

	public function delete($obj);
}