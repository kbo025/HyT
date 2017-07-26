<?php 

namespace Navicu\Core\Domain\Repository;

/**
* 	Interfaz de la RoomRepository
*
*	@author Freddy Contreras <freddycontreras3@gmail.com>
*	@author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
*	@version 21-05-2015
*/
interface RoomRepository
{
    /**
     * La siguiente función se encarga de consultar una habitación
     * dado un slug y un id de habitación
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $slug
     * @param $roomId
     * @version 20/11/2015
     *
     */
    public function findOneBySlugRoom($slug, $roomId);

    public function persistObject($room);

    public function flushObject();
}