<?php
namespace Navicu\Core\Application\UseCases\Admin\DropDailyFromRoom;
/**
 * Created by Isabel Nieto <isabelcnd@gmail.com>.
 * User: user03
 * Date: 29/04/16
 * Time: 03:11 PM
 */
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Domain\Adapter\ArrayCollection;

/**
 * Comando para eliminar dentro del establecimiento ciertos
 * packs de la habitacion.
 *
 * @author Isabel Cristina Nieto Danis <isabelcnd@gmail.com>
 * @author Currently Working: Isabel Nieto.
 */
class DropDailyFromRoomCommand implements Command {

    /**
     * @var ArrayCollection $dates conjunto de fechas solicitadas
     */
    private $dates;

    /**
     * @var ArrayCollection $packages conjunto de dailyPacks a eliminar
     */
    private $packages;

    /**
     * @var ArrayCollection $rooms conjunto de dailyRooms a eiminar
     */
    private $rooms;

    /**
     * DropDailyFromRoomCommand constructor.
     * @param object $data conjuto de fechas con dailys a eliminar
     */
    public function __construct($data)
    {
        $this->packages = $data['idPackages'];
        $this->dates = $data['dates'];
        $this->rooms = $data['idRooms'];
    }
    /**
     *  metodo getRequest devuelve un array con los parametros del command
     *
     * @author Isabel Nieto <isabecnd@gmail.com>
     * @version 05-05-2016
     * @return  array
     */
    public function getRequest()
    {
        return array(
            'dates' => $this->dates,
            'packages' => $this->packages,
            'rooms' => $this->rooms
        );
    }
}