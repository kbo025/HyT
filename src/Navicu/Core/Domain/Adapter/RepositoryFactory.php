<?php
namespace Navicu\Core\Domain\Adapter;

use Navicu\Core\Application\Contract\RepositoryFactoryInterface;

/**
* La clase siguiente hace uso del conntenedor de servicio para
* hacer llamado al servicio de RepositoryFactory.
*
* @author Joel D. Requena P. <Joel.2005.2@gmail.com>
* @author Currently Working: Joel D. Requena P.
*/
class RepositoryFactory 
{
    /**
     * Metodo que hace uso del RepositoryFactory del framework.
     * 
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param string $code
	 */
    public static function get($entity) {
        global $kernel;
        return $kernel->getContainer()->get('DbRepositoryFactory')->get($entity);
    }
}