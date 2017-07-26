<?php
namespace Navicu\Core\Domain\Repository;

use Navicu\Core\Domain\Model\Entity\Restaurant;
/**
 *	Interfaz de la RestaurantRepository
 *
 *	@author Freddy Contreras <freddycontreras3@gmail.com>
 *	@author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 */
interface RestaurantRepository
{
    /**
     * La siguiente función retorna los restaurantes de un
     * establecimiento dado un slug
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $slug
     * @return array
     * @version 18/11/2015
     */
    public function findBySlug($slug);

    /**
     *  devuelve un array con los tipos de buffet o carta que se manejan en el dominio
     *
     *	@author Gabriel Camacho <kbo025@gmail.com>
     *	@author Currently Working: Gabriel Camacho
     *
     *  @return Array
     */
    public function getBuffetCartaTypesArray();

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
     * @param Restaurant $instance
     */
    public function remove(Restaurant $instance);

    /**
     * crea o guarda una instancia
     *
     * @param Restaurant $instance
     */
    public function save(Restaurant $instance);
}
