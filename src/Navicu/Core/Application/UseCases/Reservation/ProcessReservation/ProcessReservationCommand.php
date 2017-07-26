<?php
namespace Navicu\Core\Application\UseCases\Reservation\ProcessReservation;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\PaymentGateway;
use Navicu\Core\Domain\Adapter\EntityValidationException;
use Navicu\Core\Domain\Model\Entity\ClientProfile;
use Navicu\Core\Application\Contract\CommandBase;


/**
 * Commando de 'Procesar Reservacion'
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho <kbo025@gmail.com>
 * @version 03/09/2015
 */
class ProcessReservationCommand extends CommandBase implements Command
{
    /**
     * slug que identifica el usuario que esta haciendo la reservacion
     * @var string
     */
    protected $slug;

    /**
     * public_id de la reservacion
     */
    protected $idReservation;

    /**
     * contiene la direccion ip del cliente
     *
     * @var string
     */
    protected $ip;

    /**
     * contiene el objeto que establece la comunicacion con la entidad que procesa el pago
     *
     * @var paymentGateway
     */
    protected $paymentGateway;

    /**
     * array de pagos
     *
     * @var paymentGateway
     */
    protected $payments;

    /**
     * usuario con el cual se relacionará la reserva
     *
     * @var object
     */
    protected $user;

    /**
     * datos de la persona que se hospeda, es null si el que se hospeda es el cliente
     */
    protected $isOwner = false;

    /**
     * indica si el tiempo de espera por la reserva bloqueada expiró
     */
    //private $timeoutExpired;

    /**
     * indica que sean validos los datos de resgistro del huesped
     */
    //private $valid_guest;

    /**
     * indicador de la moneda en la que se debe procesar el pago
     */
    protected $currency;

    /**
     * indica si se debe decrementar la disponibilidad o no
     */
    protected $decreaseAvailability;

    /**
     * indica si es una agencia de viaje la que esta realizadon el pago
     */
    protected $isAavv;

    /**
     * esta funcion evalua que el array de datos de las habitaciones a reservar este completo
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 17/09/2015
     * @param array $context
     * @return bool
     */
    public function validateRooms($context = null) {
        $valid = true;
        if (empty($this->idReservation)) {
            $valid = !empty($this->rooms);
            if ($valid) {
                foreach ($this->rooms as $room) {
                    //$valid = $valid && !empty($room['roomName']);
                    $valid = $valid && !empty($room['idRoom']) && is_integer($room['idRoom']);
                    //$valid = $valid && isset($room['idBedsType']);
                    $valid = $valid && !empty($room['packages']) && is_array($room['packages']);
                    if ($valid) {
                        foreach ($room['packages'] as $pack) {
                            $valid = $valid && !empty($pack['idPack']) && is_integer($pack['idPack']);
                            //$valid = $valid && !empty($pack['namePack']);
                            $valid = $valid && !empty($pack['numberPack']) && is_numeric($pack['numberPack']); /*&& is_integer($pack['numberPack'])*/
                            $valid = $valid && !empty($pack['numberPeople']) && is_numeric($pack['numberPeople']); /*&&  is_integer($pack['numberPeople'])*/
                            $valid = $valid && !empty($pack['idCancellationPolicy']) && is_numeric($pack['idCancellationPolicy']); /*&& is_integer($pack['idCancellationPolicy'])*/
                        }
                    }
                }
            }
            if (isset($context) && !$valid)
                $context->buildViolation('incorrect data!')
                    ->atPath('rooms')
                    ->addViolation();
        }
        return $valid;
    }

    /**
     * esta funcion evalua que el array de datos de los pagos este completo
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 17/09/2015
     * @param array $context
     * @throws EntityValidationException
     * @return bool
     */
    public function validatePayments()
    {
        //try {
            $valid = !empty($this->payments);
            if ($valid) {
                foreach ($this->payments as $payment) {
                    $this->paymentGateway->validateRequestData($payment);
                }
            }
            return $valid;
//        } catch (EntityValidationException $e) {
//            return false;
//        } catch (\Exception $e) {
//            return false;
//        }
    }

    public function validateGuest() {
        return empty($this->guest) ?
            true :
            !empty($this->guest['identityCard']) && !empty($this->guest['fullName']);
    }
}
