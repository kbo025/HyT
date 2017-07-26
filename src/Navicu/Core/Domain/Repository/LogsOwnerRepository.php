<?php 

namespace Navicu\Core\Domain\Repository;

/**
*   Interfaz de la LogsOwnerRepository
*
*   @author Freddy Contreras <freddycontreras3@gmail.com>
*   @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
*   @version 25-08-2015
*/
interface LogsOwnerRepository
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