<?php
namespace Navicu\Core\Domain\Adapter;

/**
 * Excepción que será lanzada cuando se procesa una reserva y el hotel se queda sin disponibilidad
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @version 16-10-2015
 */
class NotAvailableException extends \Exception
{

    private $date;

    /**
     *   constructor
     */
    public function __construct($date, $msj, $code=null) {
        $this->date = $date;
        parent::__construct($msj,$code);
    }

    public function getDate()
    {
        return $this->date;
    }
}