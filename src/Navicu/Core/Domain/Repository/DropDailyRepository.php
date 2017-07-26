<?php 

namespace Navicu\Core\Domain\Repository;

/**
 *   Interfaz de la DropDailyRepository
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
*/
interface DropDailyRepository
{
    public function persistObject($dropDaily);

    public function flushObject();
}