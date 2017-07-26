<?php 

namespace Navicu\Core\Domain\Repository;

/**
* 	Interfaz de la RateByPeopleRepository
*
*	@author Freddy Contreras <freddycontreras3@gmail.com>
*	@author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
*	@version 21-05-2015
*/
interface RateByPeopleRepository
{
    /**
     * La siguiente función retorna el monto de cobró por persona
     * dado un número de personas
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $roomId
     * @param $numberPeople
     * @return mixed
     */
    public function findAmountByRoomPeople($roomId, $numberPeople);
}