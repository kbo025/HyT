<?php
namespace Navicu\Core\Application\UseCases\Reservation\CalculateRateReservation;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\PaymentGateway;
use Navicu\Core\Domain\Adapter\EntityValidationException;
use Navicu\Core\Domain\Model\Entity\ClientProfile;


/**
 * Commando de 'Procesar Reservacion'
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho <kbo025@gmail.com>
 * @version 03/09/2015
 */
class CalculateRateReservationCommand implements Command
{
    /**
     * slug que identifica el usuario que esta haciendo la reservacion
     * @var string
     */
    protected $slug;

    /**
     * fecha de checkin
     * @var string
     */
    protected $checkinReservation;

    /**
     * fecha de checkout
     * @var string
     */
    protected $checkoutReservation;

    /**
     * Cantidad de adultos involucrados en la reserva
     * @var integer
     */
    protected $numberAdults;

    /**
     * Cantidad de niños involucrados en la reserva
     * @var integer
     */
    protected $numberChildren;

    /**
     *
     * @var float
     */
    protected $rooms;

    /**
     * usuario con el cual se relacionará la reserva
     *
     * @var object
     */
    private $typeIdentity;

    /**
     * 	constructor de la clase
     *	@param $request Array
     */
	public function __construct($request, $payment = null)
	{
        foreach($request as $attName => $attVal)
            $this->$attName = $attVal;
	}

    /**
     *  Método getRequest devuelve un array con los parametros del command
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 03/09/2015
     * @return  Array
     */
    public function getRequest()
    {
        return [
            'slug' => $this->slug ,
            'checkinReservation' => $this->checkinReservation ,
            'checkoutReservation' => $this->checkoutReservation ,
            'numberAdults' => $this->numberAdults ,
            'numberChildren' => $this->numberChildren ,
            'rooms' => $this->rooms ,
            'typeIdentity' => $this->typeIdentity,
        ];
    }

    /**
     *  Método get devuelve el atributo del comando que se pasa por parametro
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @param String $att
     * @version 03/09/2015
     * @return  Array
     */
    public function get($att)
    {
        if(isset($this->$att))
            return $this->$att;
        else
            return null;
    }

    /**
     * set user
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 07/10/2015
     * @param ClientProfile $user
     */
    public function setDocumentType($dt)
    {
        $this->documentType = $dt;
    }
}
