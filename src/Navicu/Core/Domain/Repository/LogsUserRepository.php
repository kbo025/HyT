<?php
/**
 * Created by PhpStorm.
 * User: Isabel Nieto <isabelcnd@gmail.com>
 * Date: 09/06/16
 * Time: 10:14 AM
 */

namespace Navicu\Core\Domain\Repository;

/**
 *   Interfaz de la LogsUserRepository
 *
 *   @author Isabel Nieto <isabelcnd@gmail.com>
 *   @author Currently Working: Isabel Nieto <isabelcnd@gmail.com>
 *   @version 09-06-16
 */
interface LogsUserRepository
{
    /**
     * Esta función es usada para retornar desde la BD información del
     * historial por el slug de un establecimiento.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param Integer $slug
     * @return Array
     */
    public function findBySlug($slug);

    /**
     * Esta función es usada para retornar desde la BD información del
     * historial por el nombre del archivo log.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param String $fileName
     * @return Object
     */
    public function findByFileName($fileName);
}