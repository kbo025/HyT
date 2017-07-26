<?php
namespace Navicu\Core\Domain\Repository;

use Navicu\Core\Domain\Model\Entity\Salon;

/**
 *	Interfaz de la SalonRepository
 *
 *	@author Freddy Contreras <freddycontreras3@gmail.com>
 *	@author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 */
interface SalonRepository
{
    /**
     * La siguiente función retorna los salones de un
     * establecimiento dado un slug
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $slug
     * @return array
     * @version 18/11/2015
     */
    public function findBySlug($slug);

    /**
     * devuelve un array con los tipos de salones que se manejan en el dominio
     *
     *	@author Gabriel Camacho <kbo025@gmail.com>
     *	@author Currently Working: Gabriel Camacho
     *
     *  @return Array
     */
    public function getSalonTypesArray();


    /**
     * Devuelve una instancia dado un id
     *
     * @param integer $id
     * @return Object
     */
    public function getById($id);

    /**
     * elimina una instancia dado un id
     *
     * @param Salon $instance
     */
    public function remove(Salon $instance);

    /**
     * crea o guarda una instancia
     *
     * @param Salon $instance
     */
    public function save(Salon $instance);
}