<?php 

namespace Navicu\Core\Domain\Repository;

/**
*   Interfaz de la DailyRoomRepository
*
*   @author Freddy Contreras <freddycontreras3@gmail.com>
*   @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
*   @version 21-05-2015
*/
interface DailyRoomRepository
{
    /**
     * La función retorna un conjunto de dailyRoom dado un rango de fechas y una habitación
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $roomId
     * @param $startDate
     * @param $endDate
     * @return mixed
     * @version 01/09/2015
     */
    public function findByDatesRoomId($roomId, $startDate, $endDate, $select);
}