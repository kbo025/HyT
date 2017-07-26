<?php
/**
 * Implementación de una Clase para el manejo de las Validaciones.
 */
namespace Navicu\Core\Application\Validator\Contraints;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Navicu\Core\Domain\Adapter\RepositoryFactory;

/**
 * Clase ContainsDailyRoom
 *
 * Se define una clase y una serie de funciones necesarios para definir las
 * Validaciones necesarias para el manejo de la entidad DailyRoom.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class ContainsDailyRoom
{
    /**
     * Esta función es usada para validar dentro del objeto
     * DailyRoom si la propiedad maxNight es mayor a la propiedad
     * minNight, mientras maxNigh sea mayor a 0.
     * 
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     * 
     * @param Object DailyRoom $object
     * @return \Validator
     */
    public static function validMaxNightHighesrMinNight($object, ExecutionContextInterface $context)
    {   
        if (($object->getMinNight() > $object->getMaxNight()) and ($object->getMaxNight() > 0)) {
            $message = "max_night_higher_min_night,".$object->getMinNight();
            $context->buildViolation($message)
            ->atPath('min_night')
            ->addViolation();
        }
    }

    /**
     * Esta función es usada para validar si el objeto dailyRoom
     * ya existe para esa fecha.
     * 
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     * 
     * @param Object DailyRoom $object
     * @return \Validator
     */
    public static function validNewIdExist($object, ExecutionContextInterface $context)
    {   
        if ($object->getId() == null and $object->getRoom() != null) {
            $exist = RepositoryFactory::get('DailyRoom')
                    ->findOneByDateRoomId(
                        $object->getRoom()->getId(),
                        $object->getDate()
                        );

            if ($exist) {
                $message = "repeat_id_daily_room,".$object->getStringDate();
                $context->buildViolation($message)
                ->atPath('date')
                ->addViolation();
            }
        }
    }

    /**
     * Esta función es usada para validar dentro de un nuevo
     * objeto dailyRoom si la fecha es mayor a la fecha actual.
     * 
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     * 
     * @param Object DailyRoom $object
     * @return \Validator
     */
    public static function validOldDate($object, ExecutionContextInterface $context)
    {
        if ($object->getDate() != null and (bool)strtotime($object->getStringDate())) {

            $date1 = strtotime(date("Y-m-d"));

            $date2 = strtotime($object->getStringDate());
            
            if ($date2 < $date1) {
                $message = "date_old,".$object->getStringDate();
                $context->buildViolation($message)
                ->atPath('date')
                ->addViolation();
            }
        }
    }

    /**
     * Esta función es usada para validar si un DailyRoom
     * esta por arriba de la disponibilidad base de una habitación
     * 
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     * 
     * @param Object DailyRoom $object
     * @return \Validator
     */
    public static function validBaseAvailability($object, ExecutionContextInterface $context)
    {
        if ($object->getAvailability() != null) {
            $baseAvailability = $object->getRoom()->getBaseAvailability();
    
            if ($object->getAvailability() < $baseAvailability) {
                $message = "base_Availability,".$object->getRoom()->getBaseAvailability();
                $context->buildViolation($message)
                    ->atPath('baseAvailability')
                    ->addViolation();
            }
        }
    }
}
