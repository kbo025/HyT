<?php
/**
 * Implementación de una Clase para el manejo de las Validaciones.
 */
namespace Navicu\Core\Application\Validator\Contraints;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Navicu\Core\Domain\Adapter\RepositoryFactory;

/**
 * Clase ContainsDailyPack
 *
 * Se define una clase y una serie de funciones necesarios para definir las
 * Validaciones necesarias para el manejo de la entidad DailyPack.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class ContainsDailyPack
{
    /**
     * Esta función es usada para validar dentro del objeto
     * DailyPack si la propiedad maxNight es mayor a la propiedad
     * minNight, mientras maxNigh sea mayor a 0.
     * 
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     * 
     * @param Object DailyPack $objDailyPack
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
     * Esta función es usada para validar dentro de un nuevo
     * objeto DailyPack si la propiedad IdDailyPack dada una fecha
     * existe.
     * 
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     * 
     * @param Object DailyPack $objDailyPack
     * @return \Validator
     */
    public static function validNewIdExist($object, ExecutionContextInterface $context)
    {   
        if ($object->getId() == null and $object->getPack() != null) {
            $exist = RepositoryFactory::get('DailyPack')
                    ->findOneByPackIdDate(
                        $object->getPack()->getId(),
                        $object->getDate()
                        );

            if ($exist) {
                $message = "repeat_id_daily_pack,".$object->getStringDate();
                $context->buildViolation($message)
                ->atPath('date')
                ->addViolation();
            }
        }
    }

    /**
     * Esta función es usada para validar dentro de un nuevo
     * objeto DailyPack si la fecha es mayor a la fecha actual.
     * 
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     * 
     * @param Object DailyPack $objDailyPack
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
     * Esta función es usada para validar si la disponibilidad de
     * un dailyPack esta por debajo de la disponibilidad
     * de una habitación y al mismo tiempo verificar si dicha disponibilidad
     * de habitación existe.
     * 
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     * 
     * @param Object DailyPack $objDailyPack
     * @return \Validator
     */
    public static function validAvailabilityRoom($object, ExecutionContextInterface $context)
    {
        $date = $object->getDate();
        $idRoom = $object->getPack()->getRoom()->getId();
        $dailyRoom = RepositoryFactory::get('DailyRoom')
                        ->findOneByDateRoomId(
                                            $idRoom,
                                            $date
                                        );

        if ($dailyRoom) {
            if ($dailyRoom->getAvailability() < $object->getSpecificAvailability()) {
                $message = "availability_higher,".$object->getSpecificAvailability();
                $context->buildViolation($message)
                ->atPath('specificAvailability')
                ->addViolation();
            } 
        }
    }

    /**
     * Esta función es usada para validar si la disponibilidad de
     * un dailyPack y la sumas de los dailyPack de los otros servicio esta
     * por debajo de la disponibilidad una habitación.
     * 
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     * 
     * @param Object DailyPack $objDailyPack
     * @return \Validator
     */
    public static function validAvailabilityPack($object, ExecutionContextInterface $context)
    {
        $date = $object->getDate();
        $idRoom = $object->getPack()->getRoom()->getId();
        $dailyRoom = RepositoryFactory::get('DailyRoom')
                        ->findOneByDateRoomId(
                                            $idRoom,
                                            $date
                                        );

        if ($dailyRoom) {
            $dailyPackages = RepositoryFactory::get('DailyPack')
                ->findByRoomDate(
                    $idRoom,
                    $date
                );
    
            $a = !$object->getId() ? $object->getSpecificAvailability() : 0;
            for ($dp = 0; $dp < count($dailyPackages); $dp++) {
                if ($dailyPackages[$dp]->getId() != $object->getId()) {
                    $a += $dailyPackages[$dp]->getSpecificAvailability() ? $dailyPackages[$dp]->getSpecificAvailability() : 0;
                } else {
                    $a += $object->getSpecificAvailability() ? $object->getSpecificAvailability() : 0;
                }
            }

            if ($a < $dailyRoom->getAvailability() and $dailyRoom->getAvailability()) {
                $message = "availability_low,".$a;
                $context->buildViolation($message)
                ->atPath('specificAvailability')
                ->addViolation();
            }
        }
    }

    /**
     * Esta función es usada para validar si el rango de minimo y maximo de noche
     * se mantiene en el rango del minimo y maximo de noche de la habitación.
     * 
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     * 
     * @param Object DailyPack $objDailyPack
     * @return \Validator
     */
    public static function validRankMinNightMaxNight($object, ExecutionContextInterface $context)
    {
        $dailyRoom = RepositoryFactory::get('DailyRoom')
                    ->findOneByDateRoomId(
                        $object->getPack()->getRoom()->getId(),
                        $object->getDate()
                    );

        if($dailyRoom) {
            $minNightBase = $dailyRoom->getMinNight();
            $maxNightBase = $dailyRoom->getMaxNight();
        

            if ($object->getMinNight() < $minNightBase || $object->getMaxNight() > $maxNightBase) {
                $message = "no_rank_night,".$object->getMinNight()."-".$object->getMaxNight();
                $context->buildViolation($message)
                ->atPath('rankNight')
                ->addViolation();
            }
        }
    }
}
