<?php
namespace Navicu\Core\Application\UseCases\Admin\EditReservationDetails;

use Navicu\Core\Application\Contract\Command;


/**
 *
 * La siguiente clase impleta la modificación de un commnado
 *
 * Class EditReservationDetailsCommand
 * @package Navicu\Core\Application\UseCases\Admin\EditReservationDetails
 */
class EditReservationDetailsCommand implements Command
{
    /**
     * Representa el id publico de la reserva que se editará
     * @var string
     */
    private $public_id;

    /**
     * Representa los datos que se cambiaran de la reserva
     * @var string json
     */
    private $data;

    /**
     * EditReservationDetailsCommand constructor.
     * @param string $public_id
     * @param string $data
     */
    public function __construct($public_id, $data)
    {
        $this->public_id = $public_id;
        $this->data = $data;
    }

    /**
     *  metodo getRequest devuelve un array con los parametros del command
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 05-05-2015
     * @return  Array
     */
    public function getRequest()
    {
        return
            array(
                'public_id' => $this->public_id,
                'data' => $this->data
            );
    }

    /**
     * @return mixed
     */
    public function getPublicId()
    {
        return $this->public_id;
    }

    /**
     * @param mixed $public_id
     */
    public function setPublicId($public_id)
    {
        $this->public_id = $public_id;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}