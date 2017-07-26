<?php

namespace Navicu\Core\Application\UseCases\Web\CreateFlightReservation;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

class CreateFlightReservationCommand extends CommandBase implements Command {

	/**
	 * Array con los datos de los vuelos
	 *
	 * @var Array
	 */
	protected $flights;

	/**
	 * Array con los datos de pasajeros (cantidad de niños y de adultos)
	 *
	 * @var Array
	 */
	protected $passengers;

	/**
	 * Moneda establecida en la session
	 *
	 * @var string
	 */
	protected $currency;
}