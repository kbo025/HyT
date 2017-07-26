<?php 

namespace Navicu\Core\Domain\Repository;

/**
*   Interfaz de la DailyPackRepository
*
*   @author Freddy Contreras <freddycontreras3@gmail.com>
*   @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
*   @version 25-08-2015
*/
interface DailyPackRepository
{
    /**
     * La siguiente función retorna el conjunto de dailyPack en un rango de fechas
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>     *
     * @param $packId
     * @param $startDate
     * @param $endDate
     * @return mixed
     * @version 01/08/2015
     */
    public function findByDatesRoomId($packId, $startDate, $endDate);
    
    /**
     * La siguiente función retorna el conjunto de dailyPack en un rango de fechas
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $packId
     * @param $startDate
     * @param $endDate
     * @param $select
     * 
     * @return mixed
     * @version 19/08/2015
     */
    public function findByDatesPackId($packId, $startDate, $endDate, $select);

    /**
     * Esta función retorna los dailyPackages dado un Id de una habitación y
     * un rango de fechas.
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     *
     * @param Integer $idRoom
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return Array
     * * @version 14/01/2016
     */
    public function findByRoomDateRange($idRoom, $startDate, $endDate);
}