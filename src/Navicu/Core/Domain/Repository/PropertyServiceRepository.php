<?php 

namespace Navicu\Core\Domain\Repository;
use Navicu\Core\Domain\Model\Entity\PropertyService;

/**
* 	Interfaz de la PropertyServiceRepository
*
*	@author Freddy Contreras <freddycontreras3@gmail.com>
*	@author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
*	@version 21-05-2015
*/
interface PropertyServiceRepository
{
    /**
     * La siguiente función retorna los servcios
     * dado un servicio padre (root) referente a un establecimiento
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $slug
     * @param $service
     * @return array
     * @version 18/11/2015
     */
    public function findByPropertyService($slug, $service);

     /**
     *  Busca una instancia de PropertyService Dado un ID
     *
     * @param integer $id
     * @retur PropertyService | null
     */
    public function getById($id);

    /**
     * persiste la entidad en la base de datos
     *
     * @param PropertyService $propertyService
     */
    public function save(PropertyService $propertyService);

    /**
     * elimina la entidad de la base de datos
     *
     * @param PropertyService $propertyService
     */
    public function remove(PropertyService $propertyService);

    /**
     * La función retorna una instancia dado un id del establecimiento y un id de tipo se dervicio
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     *
     * @param $propertyId
     * @param $typeId
     *
     * @return PropertyService | null
     * @version 22/12/2015
     */
    public function findByPropertyType($propertyId, $typeId);
}