<?php

namespace Navicu\InfrastructureBundle\Resources\Services;

/**
 * Clase ManagerBD
 *
 * Se define una clase y una serie de funciones de persistencia en el manejar de BD
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 *
 */
class ManagerBD
{

    /**
     * @var Variable contiene el manejador de Base de datos
     */
    protected $managerBD;

    public function __construct($managerBD)
    {
        $this->managerBD = $managerBD;
    }

    /**
     * La función persiste un objecto
     *
     * @param $object
     */
    public function persist($object)
    {
        $this->managerBD->persist($object);
    }

    /**
     * La función guarda en la BD los objectos anteriormente persistidos
     */
    public function save()
    {
        $this->managerBD->flush();
    }

    /**
     * La función borra un objecto
     *
     * @param $object
     */
    public function delete($object)
    {
        $this->managerBD->remove($object);
    }
}