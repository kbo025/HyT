<?php
namespace Navicu\Core\Application\UseCases\Admin\ListReservation;

use Navicu\Core\Application\Contract\Command;

/**
 * El siguiente comando contiene los datos necesario para ejecutar
 * el caso de uso GetDetailsProperty (Mostrar un listado de todas las reservas segun un status, checkin, checkout, reservationDate, word)
 * @author Jose Agraz <jaagraz@navicu.com>
 * @version 22/01/2016
 */

class ListReservationCommand implements Command
{

    protected $reservationStatus;

    protected $checkIn;

    protected $checkOut;

    protected $reservationDate;

    protected $idReservation;

    protected $word;

    protected $user;

    /**
     * Constructor de la clase
     * @param $data
     */
    public function __construct($data = null)
    {
        $this->reservationStatus = isset($data['reservationStatus']) ? $data['reservationStatus'] : null;
        $this->checkIn = isset($data['checkIn']) ? $data['checkIn'] : null;
        $this->checkOut = isset($data['checkOut']) ? $data['checkOut'] : null;
        $this->reservationDate = isset($data['reservationDate']) ? $data['reservationDate'] : null;
        $this->idReservation = isset($data['idReservation']) ? $data['idReservation'] : null;
        $this->word = isset($data['word']) ? $data['word'] : null;
        $this->user = isset($data['user']) ? $data['user'] : null;
    }

    public function getRequest()
    {
        return [
                'reservationStatus' => $this->reservationStatus,
                'checkIn' => $this->checkIn,
                'checkOut' => $this->checkOut,
                'reservationDate' => $this->reservationDate,
                'idReservation' => $this->idReservation,
                'word' => $this->word,
                'user' => $this->user
            ];
    }

    /**
     * Método get del atributo reservationStatus
     * @return reservationStatus
     */
    public function getReservationStatus()
    {
        return $this->reservationStatus;
    }

    /**
     * Método get del atributo checkIn
     * @return checkIn
     */
    public function getCheckIn()
    {
        return $this->checkIn;
    }


    /**
     * Método get del atributo checkOut
     * @return checkOut
     */
    public function getCheckOut()
    {
        return $this->checkOut;
    }

    /**
     * Método get del atributo reservationDate
     * @return reservationDate
     */
    public function getReservationDate()
    {
        return $this->reservationDate;
    }

    /**
     * Método get del atributo idReservation
     * @return idReservation
     */
    public function getIdReservation()
    {
        return $this->idReservation;
    }

    /**
     * Método get del atributo word
     * @return word
     */
    public function getWord()
    {
        return $this->word;
    }

    /**
     * @return null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param null $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}