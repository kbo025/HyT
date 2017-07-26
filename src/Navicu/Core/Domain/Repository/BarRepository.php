<?php
namespace Navicu\Core\Domain\Repository;

use Navicu\Core\Domain\Model\Entity\Bar;

/**
 *	@author Gabriel Camacho <kbo025@gmail.com>
 *	@author Currently Working: Gabriel Camacho
 */
interface BarRepository {

    /**
     * La siguiente función retorna los bares de un
     * establecimiento dado un slug
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $slug
     * @return array
     * @version 18/11/2015
     */
    public function findBySlug($slug);

    /**
     *  devuelve un array con los tipos de bares que se manejan en el dominio
     *
     *	@author Gabriel Camacho <kbo025@gmail.com>
     *	@author Currently Working: Gabriel Camacho
     *
     *  @return Array
     */
    public function getBarTypesArray();

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
     * @param Bar $instance
     */
    public function remove(Bar $instance);

    /**
     * crea o guarda una instancia
     *
     * @param Bar $instance
     */
    public function save(Bar $instance);
} 
