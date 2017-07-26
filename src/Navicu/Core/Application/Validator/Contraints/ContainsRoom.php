<?php
/**
 * Implementación de una Clase para el manejo de las Validaciones.
 */
namespace Navicu\Core\Application\Validator\Contraints;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Navicu\Core\Domain\Adapter\RepositoryFactory;

/**
 * Clase ContainsRoom
 *
 * Se define una clase y una serie de funciones necesarios para definir las
 * Validaciones necesarias para el manejo de la entidad Room.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class ContainsRoom
{
    /**
     * Esta función es usada para validar dentro del objeto
     * Room si la propiedad MaxPeople es mayor a la propiedad
     * MinPeople, mientras MaxPeople sea mayor a 0.
     * 
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     * 
     * @param Object Room $object
     * @return \Validator
     */
    public static function validMaxPeopleHighesrMinPeople($object, ExecutionContextInterface $context)
    {   
        if (($object->getMinPeople() > $object->getMaxPeople()) and ($object->getMaxPeople() > 0)) {
            $message = "max_people_higher_min_people,".$object->getMaxPeople();
            $context->buildViolation($message)
            ->atPath('max_people')
            ->addViolation();
        }
    }

    /**
     * Esta función es usada para validar dentro del establecimiento si existe otra
     * habitación con el mismo nombre y caracteristica.
     * 
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     * 
     * @param Object Room $object
     * @return \Validator
     */
    public static function validRoomUnique($object, ExecutionContextInterface $context)
    {   
        if (!is_null($object->getMaxPeople()) and !is_null($object->getType())) {
            $rooms = $object->getProperty()->getRooms();
			for ($r = 0; $r < count($rooms); $r++) {

                if ($object->getType()->getId() == $rooms[$r]->getType()->getId() and
                $object->getMaxPeople() == $rooms[$r]->getMaxPeople() and
				$object->getId() != $rooms[$r]->getId() and
				$rooms[$r]->getIsActive() == true){

                    $message = "no_room_unique,".$object->getId();
                    $context->buildViolation($message)
                    ->atPath('roomId')
                    ->addViolation();

                }

            }
        }
    }

    /**
     * Esta función es usada para validar si el numero de habitaciones no supera
     * el total de numero de habitaciones ofrecidas por el establecimiento.
     * 
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     * 
     * @param Object Room $object
     * @return \Validator
     */
    public static function validAmountRoom($object, ExecutionContextInterface $context)
    {

		$rooms = $object->getProperty()->getRooms();

		$amountRoom = $object->getId()== null ? $object->getAmountRooms() : 0;
		for ($r = 0; $r < count($rooms); $r++) {
			if ($rooms[$r]->getIsActive()) {
				if ($rooms[$r]->getId() == $object->getId()) {
					$amountRoom = $amountRoom + $object->getAmountRooms();
				} else {
					$amountRoom = $amountRoom + $rooms[$r]->getAmountRooms();
				}
			}
		}

        if ($amountRoom > $object->getProperty()->getAmountRoom()) {
            $message = "room_number_surpassed,".$amountRoom;
            $context->buildViolation($message)
            ->atPath('amount_room')
            ->addViolation();
		}
    }
}
