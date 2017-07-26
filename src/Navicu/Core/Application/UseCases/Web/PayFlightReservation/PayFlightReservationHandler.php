<?php

namespace Navicu\Core\Application\UseCases\Web\PayFlightReservation;


use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\EmailInterface;
use Navicu\Core\Domain\Model\Entity\FlightPayment;
use Navicu\Core\Domain\Model\Entity\FlightReservation;
use Navicu\Core\Application\Contract\ResponseCommandBus;

class PayFlightReservationHandler implements Handler
{

	/**
	 *   instancia del repositoryFactory
	 *
	 *   @var RepositoryFactoryInterface $rf
	 */
	protected $rf;

	/**
	 * el comando que llama al caso de uso
	 * @var Command command
	 */
	protected $command;

	/**
	 * La variable contiene la funcionalidades del servicio de email
	 *
	 * @var EmailInterface $emailService
	 */
	protected $emailService;

	/**
	 * Método Set del la interfaz del serivicio Email
	 * @param EmailInterface $emailService
	 */
	public function setEmailService(EmailInterface $emailService)
	{
		$this->emailService = $emailService;
	}

	public function handle(Command $command, RepositoryFactoryInterface $rf)
	{
		$this->command = $command;
		$request = $command->getRequest();
		$flightReservationRp = $rf->get('FlightReservation');
		$flightReservation = $flightReservationRp->findByPublicId($request['publicId']);

		$payments = $this->completePaymentInfo($flightReservation,$request['payments']);

		$success = false;

		$processedPayments = $this->processPayments($flightReservation, $payments, $success);

		if ($success) {
			$command->get('flightBookingService')->confirmFlightItinerary([]);
			//TODO: enviar email
			$flightReservationRp->save($flightReservation);
			return new ResponseCommandBus(201, 'OK');
		} else {
			$command->get('flightBookingService')->cancelFlightItinerary([]);
			return new ResponseCommandBus( 400, 'Bad Request',
				[ 'errorStatus' => 3, 'message' => $processedPayments ] );
		}
	}

	private function completePaymentInfo(FlightReservation $flightReservation,$payment)
	{

		$response = [];
		//$reservationId = $flightReservation->getCode();
		$reservationId = 'DFT234';

			//$amount = $flightReservation->getTotalToPay();
			$amount = 115000;
			if (isset($payment['amount']))
				$amount = $payment['amount'];
			$amount = number_format(round($amount,2),2);
			$response[] = array_merge(
				$payment,
				[
					'description' => "Pago de la compra N° ".$reservationId." ",
					'ip' => $this->command->get('ip'),
					'amount' => $amount,
					'date' => \date('d-m-Y'),
					'ExpirationDate' => $payment['expirationMonth'] . '/' . $payment['expirationYear'],
				]
			);

		return $response;
	}

	private function processPayments(FlightReservation $reservation, $payments ,&$success)
	{
		$data = [];
		$pgw = $this->command->get('paymentGateway');
		$processedPayments = $pgw->processPayments($payments);
		$success = $pgw->isSuccess();

		foreach ($processedPayments as $payment) {
			$success = $payment['code'] == '201' && $payment['success'];
			$pago = new FlightPayment();
			$pago = $pago->setCode($payment['code'])
			             ->setDate(new \DateTime())
			             ->setReference($payment['reference'])
			             ->setAmount($payment['amount'])
			             ->setReservation($reservation)
			             ->setIpAddress($this->command->get('ip'))
			             ->setHolder($payment['holder'])
			             ->setHolderId($payment['holderId'])
			             ->setState($payment['status'])
			             ->setType($pgw->getTypePayment())
			             ->setResponse($payment['response']);

//			if (isset($payment['currency'])) {
//				$pago
//					->setAlphaCurrency($payment['currency'])
//					->setDollarPrice($payment['dollarPrice'])
//					->setNationalPrice($payment['nationalPrice']);
//			}

			$reservation->addPayment($pago);
			$data[] = $payment;
		}
		return $data;
	}

}