<?php

namespace Navicu\Core\Application\UseCases\Web\PayFlightReservation;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

class PayFlightReservationCommand extends CommandBase implements Command {

	/**
	 * contiene el objeto que establece la comunicacion con la entidad que procesa el pago
	 *
	 * @var paymentGateway
	 */
	protected $paymentGateway;

	/**
	 * array de pagos
	 *
	 * @var Array
	 */
	protected $payments;

	/**
	 * Numero que identifica la reserva de boletos aereos
	 * @var string
	 */
	protected $reservationNumber;

	protected $ip;

	protected $currency;

	protected $flightBookingService;

}