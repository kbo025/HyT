<?php
/**
 * Implementacion de una Entidad.
 */
namespace Navicu\Core\Domain\Model\Entity;

use Navicu\Core\Domain\Adapter\ArrayCollection;
use Navicu\Core\Domain\Adapter\EntityValidationException;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Model\ValueObject\PublicId;
use Navicu\Core\Domain\Adapter\StateMachine;
use Navicu\Core\Application\Contract\PaymentGateway;

/**
 * Clase Reservation
 *
 * La clase representa las reservas realizadas en el sistemas
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 * @version 20/08/2015
 */
class Reservation extends StateMachine
{
    /**
     * @var integer
     */
    private $id;

    /**
     * Representa el identificador de la reserva
     * @var string
     */
    private $public_id;

    /**
     * Fecha de llegada del cliente
     * @var \DateTime
     */
    private $date_check_in;

    /**
     * Fecha de salida del cliente
     * @var \DateTime
     */
    private $date_check_out;

    /**
     * Números de niños asociado a la reserva
     * @var integer
     */
    private $child_number;

    /**
     * Números de adultos asociado a la reserva
     * @var integer
     */
    private $adult_number;

    /**
     * Peticiones adicionales por parte del cliente
     * @var string
     */
    private $special_request;

    /**
     * Total de monto a pagar por el usuario
     * @var float
     */
    private $total_to_pay;

    /**
     * Es la url por donde el usuario podrá accesar a los datos de la reserva
     * @var string
     */
    private $hash_url;

    /**
     * Conjunto de servicios (pack) asociadas a la reserva
     * @var ArrayCollection
     */
    private $reservation_packages;

    /**
     * Cliente propietario de la reserva
     * @var \Navicu\Core\Domain\Model\Entity\ClientProfile
     */
    private $client_id;

    /**
     * Relación con el establecimiento en el cual se esta haciendo la reserva
     * @var Property
     */
    private $property_id;

    /**
     * coleccion de pago hechos por la reservacion
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $payments;

    /**
     * Atributo contiene la fecha que se realiza la reserva
     *
     * @var \DateTime
     */
    private $reservation_date;

    /**
     * representa el estado de una reserva donde:
     *
     * 0: prereserva: reserva en espera de pago por parte del cliente
     * 1: por confirmar: pago a la espera de confirmación por parte de navicu
     * 2: pago confirmado: reserva con pago confirmado
     * 3: Reserva Cancelada: reserva que no procede por diferentes razones
     *
     * @var integer
     */
    private $status = 0;

    /**
     * codigo que identifica el tipo de reserva (TDC, transferencia, etc...)
     *
     * 1: TDC
     * 2: transferencia
     *
     * @var integer
     */
    private $payment_type;

    /**
     * Manejo del historial de cambios de la reserva.
     *
     * @var integer
     */
    private $change_history;

    /**
     * Manejo del historial de cambios de la reserva.
     *
     * @var integer
     */
    private $current_state;

    /**
     * @var array
     */
    private $guest;

    /**
     * codigo prefijo del public_id, indica caracteristicas de la reserva
     *
     * @var string
     */
    private $code_prefix;

    /**
     * direccion ip publica del cliente que hace la reserva
     *
     * @var string
     */
    private $ip_address;

    /**
     * bloqueos de disponibilidad para esta reserva
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $lockeds;

    /**
     * observaciones relevantes para la reserva
     *
     * @var string
     **/
    //private $observations;

    /**
     * la comisión cobrada al momento de realizar la reserva
     * @var float
     */
    private $discount_rate = 0;

    /**
     * Tarifa para el hotelero
     * @var float
     */
    private $net_rate = 0;

    /**
     * porcentaje cobrado por impuestos
     * @var float
     */
    private $tax;


    /**
     *  indica si loos valores fueron redondeados o no
     */
    private $round = false;

    /**
     * informacion cambiaria para moneda extranjera
     *
     * @var array
     */
    private $currency_convertion_information;

    /**
     * Propiedad que esta ligada al grupo de la reserva a la cual esta pertenece
     * 
     * @var \Navicu\Core\Domain\Model\Entity\getAavvReservationGroup
     */
    private $aavv_reservation_group;

    /**
    * indica el estado actual del objeto
    */
    protected $state;

    /**
    * lista de los estados posibles para una reserva
    */
    protected $states = [
        'initial' => null,  
        'awaiting' => 0,
        'in_confirmation_process' => 1,
        'confirmed' => 2,
        'not_confirmed' => 3,
    ];

    /**
    * lista de las transiciones posibles para una reserva
    */
    protected $transitions = [
        'create_action' => [null,0],
        'canceled_for_not_concreted' => [null,3],
        'awaiting_prereservation_action' => [0,1], 
        'awaiting_reservation_action' => [0,2],
        'awaiting_admin_approved_action' => [1,2],        
        'automatic_canceled ' => [0,3],
        'automatic_by_admin ' => [1,3],
    ];

    /**
     * taza de descuento aplicada por ser una reserva hecha por una agencia de viaje
     * 
     * @var float
     */
    private $discount_rate_aavv = 0;

    /**
     * Propiedad creada unicamente para las reservas realizadas desde aavv sin guardarse en la bd
     * para obtener el calculo del iva y la reserva sin iva
     *
     * @var float
     */
    protected $net_rate_aavv;

    /**
     * Constructor
     * @param bool $reservationForAAVV, parametro unicamente para la creacion
     * de reservas desde una agencia de viajes
     */
    public function __construct($reservationForAAVV = false)
    {
        $this->reservation_packages = new ArrayCollection();
        $this->change_history = new ArrayCollection();
        $this->payments = new ArrayCollection();
        // Reservas con el formato usual desde navicu
        if (strcmp($reservationForAAVV, "false") == 0)
            $this->public_id = new PublicId('dateHex');
        else // Reservas con el formato establecido para las aavv
            $this->public_id = new PublicId('dateHex', $reservationForAAVV);
        $this->round = false;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set public_id
     */
    public function setPublicId()
    {
        //este atributo se crea en el momento de persistir y no se debe modificar
    }

    /**
     * Get public_id
     *
     * @return string 
     */
    public function getPublicId()
    {
        return $this->public_id;
    }

    /**
     * Set date_check_in
     *
     * @param \DateTime $dateCheckIn
     * @return Reservation
     */
    public function setDateCheckIn(\DateTime $dateCheckIn)
    {
        if(isset($dateCheckIn))
            $this->date_check_in = $dateCheckIn;
        else
            throw new EntityValidationException('date-check-in',\get_class($this),'is_null');
        return $this;
    }

    /**
     * Get date_check_in
     *
     * @return \DateTime 
     */
    public function getDateCheckIn()
    {
        return $this->date_check_in;
    }

    /**
     * Set date_check_out
     *
     * @param \DateTime $dateCheckOut
     * @return Reservation
     */
    public function setDateCheckOut(\DateTime $dateCheckOut)
    {
        if(isset($dateCheckOut))
            $this->date_check_out = $dateCheckOut;
        else
            throw new EntityValidationException('date-check-out',\get_class($this),'is_null');
        return $this;
    }

    /**
     * Get date_check_out
     *
     * @return \DateTime 
     */
    public function getDateCheckOut()
    {
        return $this->date_check_out;
    }

    /**
     * Set child_number
     *
     * @param integer $childNumber
     * @return Reservation
     */
    public function setChildNumber($childNumber)
    {
        if (!is_integer($childNumber))
            throw new EntityValidationException('chid_number',\get_class($this),'invalid_type');
        else
            $this->child_number = $childNumber;
        return $this;
    }

    /**
     * Get child_number
     *
     * @return integer 
     */
    public function getChildNumber()
    {
        return $this->child_number;
    }

    /**
     * Set adult_number
     *
     * @param integer $adultNumber
     * @return Reservation
     */
    public function setAdultNumber($adultNumber)
    {
        if (empty($adultNumber))
            throw new EntityValidationException('adult_numbre',\get_class($this),'is_null');
        else if (!is_integer($adultNumber))
            throw new EntityValidationException('adult_numbre',\get_class($this),'invalid_type');
        else if ($adultNumber<0)
            throw new EntityValidationException('adult_numbre',\get_class($this),'illegal_value');
        else
            $this->adult_number = $adultNumber;
        return $this;
    }

    /**
     * Get adult_number
     *
     * @return integer 
     */
    public function getAdultNumber()
    {
        return $this->adult_number;
    }

    /**
     * Set special_request
     *
     * @param string $specialRequest
     * @return Reservation
     */
    public function setSpecialRequest($specialRequest)
    {
        $this->special_request = $specialRequest;

        return $this;
    }

    /**
     * Get special_request
     *
     * @return string 
     */
    public function getSpecialRequest()
    {
        return $this->special_request;
    }

    /**
     * Set total_to_pay
     *
     * @param float $totalToPay
     * @return Reservation
     */
    public function setTotalToPay($totalToPay)
    {
        $this->total_to_pay = round($totalToPay,4);

        return $this;
    }

    /**
     * Get total_to_pay
     *
     * @return float 
     */
    public function getTotalToPay( $whitTax = true )
    {
        $aux = $whitTax ? 1 : 1 + (isset($this->tax) ? $this->tax : $this->getPropertyId()->getTaxRate());
        if($this->round)
            return round($this->total_to_pay / $aux);
        else
            return $this->total_to_pay / $aux;

    }

    /**
     * Get tax_pay
     *
     * @return float 
     */
    public function getTaxPay()
    {
        $response = $this->total_to_pay - $this->getTotalToPay(false);
        if($this->round)
            return round($response);
        else
            return $response;
    }

    /**
     * Set hash_url
     *
     * @return Reservation
     */
    public function setHashUrl()
    {
        if(empty($this->hash_url)) {
            if(!empty($this->client_id)) {
                $clientEmail = $this->client_id->getEmail();
                if(!empty($clientEmail)) {
                    if ($clientEmail instanceof EmailAddress)
                        $this->hash_url = hash("sha256", $clientEmail->toString());
                    else
                        $this->hash_url = hash("sha256", $clientEmail);
                }
            }
        }
        return $this;
    }

    /**
     * Get hash_url
     *
     * @return string 
     */
    public function getHashUrl()
    {
        return $this->hash_url;
    }

    /**
     * Add reservation_packages
     *
     * @param \Navicu\Core\Domain\Model\Entity\ReservationPack $reservationPackages
     * @return Reservation
     */
    public function addReservationPackage(\Navicu\Core\Domain\Model\Entity\ReservationPack $reservationPackages)
    {
        $this->reservation_packages[] = $reservationPackages;

        return $this;
    }

    /**
     * Remove reservation_packages
     *
     * @param \Navicu\Core\Domain\Model\Entity\ReservationPack $reservationPackages
     */
    public function removeReservationPackage(\Navicu\Core\Domain\Model\Entity\ReservationPack $reservationPackages)
    {
        $this->reservation_packages->removeElement($reservationPackages);
    }

    /**
     * Get reservation_packages
     *
     * @return ArrayCollection
     */
    public function getReservationPackages()
    {
        return $this->reservation_packages;
    }

    /**
     * Set client_id
     *
     * @param \Navicu\Core\Domain\Model\Entity\ClientProfile $clientId
     * @return Reservation
     */
    public function setClientId(\Navicu\Core\Domain\Model\Entity\ClientProfile $clientId = null)
    {
        $this->client_id = $clientId;

        return $this;
    }

    /**
     * Get client_id
     *
     * @return \Navicu\Core\Domain\Model\Entity\ClientProfile 
     */
    public function getClientId()
    {
        return $this->client_id;
    }

    /**
     * Set property_id
     *
     * @param \Navicu\Core\Domain\Model\Entity\Property $propertyId
     * @return Reservationdiscount_rate
     */
    public function setPropertyId(\Navicu\Core\Domain\Model\Entity\Property $propertyId)
    {
        $this->property_id = $propertyId;

        return $this;
    }

    /**
     * Get property_id
     *
     * @return \Navicu\Core\Domain\Model\Entity\Property 
     */
    public function getPropertyId()
    {
        return $this->property_id;
    }

    /**
     * Add payments
     *
     * @param \Navicu\Core\Domain\Model\Entity\Payment $payments
     * @return Reservation
     */
    public function addPayment(\Navicu\Core\Domain\Model\Entity\Payment $payments)
    {
        $this->payments[] = $payments;

        return $this;
    }

    /**
     * Remove payments
     *
     * @param \Navicu\Core\Domain\Model\Entity\Payment $payments
     */
    public function removePayment(\Navicu\Core\Domain\Model\Entity\Payment $payments)
    {
        $this->payments->removeElement($payments);
    }

    /**
     * Get payments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     *  convierte el OV PublicId a su representacion String para el almacenamiento
     */
    public function publicIdToString()
    {
        if ($this->public_id instanceof PublicId)
            $this->public_id = $this->public_id->toString();
    }

    /**
     * Set reservation_date
     *
     * @param \DateTime $reservationDate
     * @return Reservation
     */
    public function setReservationDate($reservationDate)
    {
        $this->reservation_date = $reservationDate;

        return $this;
    }

    /**
     * Get reservation_date
     *
     * @return \DateTime 
     */
    public function getReservationDate()
    {
        return $this->reservation_date;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Reservation
     */
    public function setStatus($status)
    {
        parent::setState($status);
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set payment_type
     *
     * @param string $paymentType
     * @return Reservation
     */
    public function setPaymentType($paymentType)
    {
        $this->payment_type = $paymentType;

        return $this;
    }

    /**
     * Get payment_type
     *
     * @return string 
     */
    public function getPaymentType()
    {
        return $this->payment_type;
    }

    /**
     * Set observations
     *
     * @param string $observations
     * @return Reservation
     */
    /*public function setObservations($observations)
    {
        $this->observations = $observations;

        return $this;
    }*/

    /**
     * Get observations
     *
     * @return string 
     */
    /*public function getObservations()
    {
        return $this->observations;
    }*/

    /**
     * Set current_state
     *
     * @param \Navicu\Core\Domain\Model\Entity\ReservationChangeHistory $currentState
     * @return Reservation
     */
    public function setCurrentState(\Navicu\Core\Domain\Model\Entity\ReservationChangeHistory $currentState = null)
    {
        $this->current_state = $currentState;

        return $this;
    }

    /**
     * Get current_state
     *
     * @return \Navicu\Core\Domain\Model\Entity\ReservationChangeHistory 
     */
    public function getCurrentState()
    {
        return $this->current_state;
    }

    /**
     * Add change_history
     *
     * @param \Navicu\Core\Domain\Model\Entity\ReservationChangeHistory $changeHistory
     * @return Reservation
     */
    public function addChangeHistory(\Navicu\Core\Domain\Model\Entity\ReservationChangeHistory $changeHistory)
    {
        $this->change_history[] = $changeHistory;

        return $this;
    }

    /**
     * Remove change_history
     *
     * @param \Navicu\Core\Domain\Model\Entity\ReservationChangeHistory $changeHistory
     */
    public function removeChangeHistory(\Navicu\Core\Domain\Model\Entity\ReservationChangeHistory $changeHistory)
    {
        $this->change_history->removeElement($changeHistory);
    }

    /**
     * Get change_history
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChangeHistory()
    {
        return $this->change_history;
    }

    /**
     * Set guest
     *
     * @param array $guest
     * @return Reservation
     */
    public function setGuest($guest)
    {
        $this->guest = $guest;

        return $this;
    }

    /**
     * Get guest
     *
     * @return array 
     */
    public function getGuest()
    {
        return $this->guest;
    }

    public function getStatusName()
    {
        $response = '';
        switch($this->status) {
            case 0: $response = 'prereserva'; break;
            case 1: $response = 'por confirmar'; break;
            case 2: $response = 'pago confirmado'; break;
            case 3: $response = 'Reserva Cancelada'; break;
        }
        return $response;
    }

    public function getStatesList()
    {
        return $this->states;
    }

    /**
     * Funcion encargada de devolver la traduccion de los estados de las reservas
     *
     * @param $stateList
     * @return array
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 2017/04/03
     */
    public function getStateNames($stateList)
    {
        $responseListName = [];
        foreach ($stateList as $key => $value) {
            switch ($key) {
                case 'awaiting':
                    $listName['id'] = $value;
                    $listName['title'] = "Pre-reserva";
//                    array_push($responseListName, $listName);
                    break;
                case 'in_confirmation_process':
                    $listName['id'] = $value;
                    $listName['title'] = "Por confirmar";
//                    array_push($responseListName, $listName);
                    break;
                case 'confirmed':
                    $listName['id'] = $value;
                    $listName['title'] = "Confirmada";
//                    array_push($responseListName, $listName);
                    break;
                case 'not_confirmed':
                    $listName['id'] = $value;
                    $listName['title'] = "Cancelada";

                    break;
                default:
                    $listName[$key] = "not defined";
                    break;
            }
            array_push($responseListName, $listName);
            $listName = [];
        }
        return $responseListName;
    }


    /**
     * Get net_rate
     *
     * @return float 
     */
    public function getNetRate($whitTax = true)
    {
        $aux = $whitTax ? 1 : 1 + (isset($this->tax) ? $this->tax : $this->getPropertyId()->getTaxRate());
        if (empty($this->net_rate)) {
            $this->net_rate = 0;
            $discountRate = empty($this->discount_rate) ?
                $this->property_id->getDiscountRate() :
                $this->discount_rate;
            if ($this->total_to_pay)
                $this->net_rate = $this->total_to_pay * (1 - $discountRate);
        }
        if ($this->round)
            return round($this->net_rate / $aux);
        else
            return $this->net_rate / $aux;
    }

    /**
     * Get net_rate
     *
     * @return float
     */
    public function getNetRateAavv($whitTax = true)
    {
        $aux = $whitTax ? 1 : 1 + (isset($this->tax) ? $this->tax : $this->getPropertyId()->getTaxRate());
        if (empty($this->net_rate_aavv)) {
            $this->net_rate_aavv = 0;
            if (isset($this->aavv_reservation_group)) {
                $discountRate = empty($this->aavv_reservation_group->getAavv()->getAgreement()->getDiscountRate()) ?
                    0.1 :
                    $this->aavv_reservation_group->getAavv()->getAgreement()->getDiscountRate();

                if ($this->total_to_pay)
                    $this->net_rate_aavv = $this->total_to_pay * (1 - $discountRate);
            }
        }
        if ($this->round)
            return round($this->net_rate_aavv / $aux);
        else
            return $this->net_rate_aavv / $aux;
    }

    public function getTaxNetRateAavv()
    {
        $tnr = $this->net_rate_aavv - $this->getNetRateAavv(false);
        if ($this->round)
            return round($tnr);
        else
            return $tnr;
    }

    public function getTaxNetRate()
    {
        $tnr = $this->net_rate - $this->getNetRate(false);
        if ($this->round)
            return round($tnr);
        else
            return $tnr;
    }

    /**
     * Set discount_rate
     *
     * @param float $discountRate
     * @return Reservation
     */
    public function setDiscountRate($discountRate)
    {
        $this->discount_rate = $discountRate;

        return $this;
    }

    /**
     * Get discount_rate
     *
     * @return float 
     */
    public function getDiscountRate()
    {
        return $this->discount_rate;
    }

    public function generateNetRate()
    {
        $this->discount_rate = $this->property_id->getDiscountRate();
        $this->tax = isset($this->tax) ? $this->tax : $this->getPropertyId()->getTaxRate();
    }

    /**
     * Set tax
     *
     * @param float $tax
     * @return Reservation
     */
    public function setTax($tax)
    {
        $this->tax = $tax;

        return $this;
    }

    /**
     * Get tax
     *
     * @return float 
     */
    public function getTax()
    {
        return $this->tax;
    }

    public function roundReservation()
    {
        $sumNetRate = 0;
        $sumSellRate = 0;
        foreach ($this->reservation_packages as $rp) {
            $sumSellRate = $sumSellRate + round($rp->getPrice());
            $sumNetRate = $sumNetRate + round($rp->getNetRate());
            $rp->setPrice(round($rp->getPrice()));
            $rp->setNetRate(round($rp->getNetRate()));
        }
        $this->total_to_pay = $sumSellRate;
        $this->net_rate = $sumNetRate;
        $this->round = true;
    }

    /**
     * Set currency_convertion_information
     *
     * @param array $currencyConvertionInformation
     * @return Reservation
     */
    public function setCurrencyConvertionInformation($currencyConvertionInformation)
    {
        return $this;
    }

    /**
     * Get currency_convertion_information
     *
     * @return array 
     */
    public function getCurrencyConvertionInformation()
    {
        return $this->currency_convertion_information;
    }

    /**
     * indica si es un pago en moneda extranjera o no
     */
    public function isForeignCurrency()
    {
        return !empty($this->currency_convertion_information);
    }

    public function setAlphaCurrency($currency)
    {
        $this->currency_convertion_information['alphaCurrency'] = $currency;

        return $this;
    }

    /**
     * indica la moneda en que se expresa el monto del pago
     */
    public function getAlphaCurrency()
    {
        return isset($this->currency_convertion_information['alphaCurrency']) ?
            $this->currency_convertion_information['alphaCurrency'] :
            null;
    }

    /**
     * indica la taza de conversion entre el dolar y el bolivar
     */
    public function getDollarPrice()
    {
        return isset($this->currency_convertion_information['dollarPrice']) ?
            $this->currency_convertion_information['dollarPrice'] :
            null;
    }

    /**
     * set del dollarRate
     */
    public function setDollarPrice($dr)
    {
        $this->currency_convertion_information['dollarPrice'] = $dr;

        return $this;
    }

    /**
     * indica la taza de conversion la moneda usada y el dolar
     */
    public function getCurrencyPrice()
    {
        return isset($this->currency_convertion_information['currencyPrice']) ?
            $this->currency_convertion_information['currencyPrice'] :
            null;
    }

    /**
     * set del currencyRate
     */
    public function setCurrencyPrice($cr)
    {
        $this->currency_convertion_information['currencyPrice'] = $cr;

        return $this;
    }

    /**
     * Set aavv_reservation_group
     *
     * @param \Navicu\Core\Domain\Model\Entity\AAVVReservationGroup $aavvReservationGroup
     * @return Reservation
     */
    public function setAavvReservationGroup(\Navicu\Core\Domain\Model\Entity\AAVVReservationGroup $aavvReservationGroup = null)
    {
        $this->aavv_reservation_group = $aavvReservationGroup;

        return $this;
    }

    /**
     * Get aavv_reservation_group
     *
     * @return \Navicu\Core\Domain\Model\Entity\AAVVReservationGroup 
     */
    public function getAavvReservationGroup()
    {
        return $this->aavv_reservation_group;
    }

    /**
     * Set code_prefix
     *
     * @param string $codePrefix
     * @return Reservation
     */
    public function setCodePrefix($codePrefix)
    {
        $this->code_prefix = $codePrefix;

        return $this;
    }

    /**
     * Get code_prefix
     *
     * @return string 
     */
    public function getCodePrefix()
    {
        return $this->code_prefix;
    }

    /**
     * Set ip_address
     *
     * @param string $ipAddress
     * @return Reservation
     */
    public function setIpAddress($ipAddress)
    {
        $this->ip_address = $ipAddress;

        return $this;
    }

    /**
     * Set discount_rate_aavv
     *
     * @param float $discountRateAavv
     * @return Reservation
     */
    public function setDiscountRateAavv()
    {

        if (empty($this->discount_rate_aavv)) {
            if(!empty($this->aavv_reservation_group)) {
                $this->discount_rate_aavv = $this
                    ->aavv_reservation_group
                    ->getAavv()
                    ->getAgreement()
                    ->getDiscountRate();
            }
        }

        return $this;
    }

    /**
     * Get ip_address
     *
     * @return string 
     */
    public function getIpAddress()
    {
        return $this->ip_address;
    }

    /**
     * Add lockeds
     *
     * @param \Navicu\Core\Domain\Model\Entity\LockedAvailability $lockeds
     * @return Reservation
     */
    public function addLocked(\Navicu\Core\Domain\Model\Entity\LockedAvailability $lockeds)
    {
        $this->lockeds[] = $lockeds;

        return $this;
    }

    /**
     * Remove lockeds
     *
     * @param \Navicu\Core\Domain\Model\Entity\LockedAvailability $lockeds
     */
    public function removeLocked(\Navicu\Core\Domain\Model\Entity\LockedAvailability $lockeds)
    {
        $this->lockeds->removeElement($lockeds);
    }

    /**
     * Get lockeds
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLockeds()
    {
        return $this->lockeds;
    }

    /**
     * esta funcion es usada para que tanto status como state mantenga el mismo valor tras una actualización
     * esto funcionará asi hasta que se pueda eliminar la columna status de reservation 
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function setStatusByState()
    {
        $this->status = $this->state;

        return $this;
    }
    
    /**
     * Get discount_rate_aavv
     *
     * @return float 
     */
    public function getDiscountRateAavv()
    {
        $this->setDiscountRateAavv();
        return $this->discount_rate_aavv;
    }

    public function getTotalForAavv()
    {
        $rate = $this->getDiscountRateAavv();
        return $this->total_to_pay * $rate;
    }

    public function setState($state)
    {
        parent::setState($state);
        $this->status = $state;

        return $this;
    }

    public function isAavvAndTransferredPay()
    {
        $empty = !empty($this->aavv_reservation_group);
        $type = $this->getPaymentType();
        $transfer = ($type == PaymentGateway::NATIONAL_TRANSFER);
        $transfer = $transfer || ($type == PaymentGateway::INTERNATIONAL_TRANSFER);
        return $empty && $transfer;  
    }
}
