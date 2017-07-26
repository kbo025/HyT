<?php
namespace Navicu\Core\Domain\Repository;

/**
 * Interfaz de CommercialProfile
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 * @version 17/03/2016
 */
interface CommercialProfileRepository
{
    /**
     * Método registra a un usuario de tipo comercial
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param CommercialProfile $commercialProfile
     */
    public function registerUser($commercialProfile);
}