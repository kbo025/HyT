<?php
namespace Navicu\Core\Application\UseCases\AAVV\Reservation\CreateReservation;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

class CreateReservationCommand extends CommandBase implements Command
{
    /**
     * @var
     */
    protected $checkIn;

    /**
     * @var
     */
    protected $checkOut;

    /**
     * @var
     */
    protected $send_email;

    /**
     * @var
     */
    protected $location;

    /**
     * @var
     */
    protected $locationSlug;

    /**
     * Arreglo con el conjunto de reservas a guardar
     * @var null
     */
    protected $properties;

    /**
     * Medio de pago de la agencia de viaje
     * @var
     */
    protected $paymentGateway;

    /**
     * @var string ip del cliente
     */    
    protected $ip;
    
    /**
     * @var string Dominio utilizado para obtener el logo de la aavv
     */
    protected $domain;

    /**
     * @var string Dominio utilizado para obtener el logo de la aavv
     */
    protected $groupId;

    /*public function __construct($data, $paymentGateway)
    {
        $this->check_in = isset($data['checkIn']) ? $data['checkIn'] : null;
        $this->check_out = isset($data['checkOut']) ? $data['checkOut'] : null;
        $this->send_email = isset($data['sendEmail']) ? $data['sendEmail'] : null;
        $this->properties_reservation = isset($data['properties']) ? $data['properties'] : null;
        $this->paymentGateway = $paymentGateway;
        $this->ip = isset($data['ip']) ? $data['ip'] : null;
        $this->location = isset($data['locationSlug']) ? $data['locationSlug'] : 1;
        $this->domain = $data['domain'];
    }*/

    /**
     * @return mixed
     */
    /*public function getPaymentGateway()
    {
        return $this->paymentGateway;
    }*/

    /**
     * @return mixed
     */
    /*public function getIp()
    {
        return $this->ip;
    }*/

    /**
     * @param mixed $ip
     */
    /*public function setIp($ip)
    {
        $this->ip = $ip;
    }*/
}