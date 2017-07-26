<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 21/12/16
 * Time: 04:19 PM
 */

namespace Navicu\Core\Domain\Repository;

/**
 * Se declaran los metodos y funciones que implementa
 * el repositorio de la entidad NvcGlobalRepository
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
*/

interface NvcGlobalRepository
{
    public function findOneByName($name);
}