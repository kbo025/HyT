<?php
namespace Navicu\Core\Domain\Repository;

/**
 * Interfaz de ClientProfileRepository
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 * @version 20/08/2015
 */
interface ClientProfileRepository
{
    public function findOneByArray($array);
    public function save($client);
}