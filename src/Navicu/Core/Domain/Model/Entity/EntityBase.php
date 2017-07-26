<?php
namespace Navicu\Core\Domain\Model\Entity;

/**
 * Clase EntityBase.
 *
 * La siguiente clase contiene metodos y funciones
 * que pueden ser comunes entre todas las entidades
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 */
class EntityBase
{
    /**
     * La siguiente función actualiza los
     * atributos de una clase dado un arreglo
     *
     * @param $data
     */
    public function setAtributes($data)
    {
        if (is_array($data)) {
            foreach($data as $att => $val) {
                $this->$att = $val;
            }
        }
    }
}
