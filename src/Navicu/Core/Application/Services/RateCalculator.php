<?php
namespace Navicu\Core\Application\Services;

use Navicu\Core\Application\Services\RateExteriorCalculator;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Model\Entity\DailyPack;
use Navicu\Core\Domain\Model\Entity\DailyRoom;
use Navicu\Core\Domain\Model\Entity\Pack;
use Navicu\Core\Domain\Model\Entity\Reservation;
use Navicu\Core\Domain\Model\Entity\ReservationPack;
use Navicu\Core\Domain\Model\Entity\Property;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Adapter\NotAvailableException;

/**
 * clase que implementa metodos estaticos para el calculo de las tarifas hechas por un cliente
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 * @version 23/10/2015
 */
class RateCalculator {

    /**
    * activa la reduccion del iva al 10% dadas ciertas circunstancias
    */
    const ACTIVATE_TAX_REDUCTION = false;

    /**
     * @var object, Arreglo de un objeto de la bd que indicara si la moneda esta activa o no
     */
    static private $rf;

    /**
     * @var object, Variable para el manejo de la fecha de la petición check-In, #Manejo de divisas.
     */
    static private $request_date;

    /**
    * identificador de la sesion mediante la cual reserva el cliente 
    */
    static private $idSession;

    /**
    * indica si hubo un recalculo por la providencia decretada
    */
    static public $recalculated;

    /**
     * RateExteriorCalculator constructor.
     * @param Date $request_date
     * @param RepositoryFactoryInterface $rf
     */
    public function __construct(RepositoryFactoryInterface $rf, $request_date = null)
    {
        self::$rf = $rf;
        self::$request_date = $request_date;
    }

    /**
     *  esta funcion resive los parametros de una reserva realizada por un cliente y calcula y carga el monto total a cobrar
     *  para esa reserva en base a estos parametros y las politicas del establesimiento
     *
     * @param RepositoryFactoryInterface $rf
     * @param Reservation $reservation
     *
     * @return boolean
     */
    public static function calculateRateReservation (RepositoryFactoryInterface $rf, Reservation $reservation, $defaultCutOff = 0, $clientCondition = false)
    {
        //obtengo la difrencia de dias dedes el checkIn al checkOut
        $diffDays =  $reservation->getDateCheckIn()
            ->diff($reservation->getDateCheckOut())->days;

        self::$idSession = is_null($reservation->getState()) ?
            [] :
            CoreSession::getSessionReservation();

        //$property = $reservation->getPropertyId();
        $amountTotal = 0;
        $totalChildren = 0;
        $totalAdults = 0;

        foreach ($reservation->getReservationPackages() as $reservationPack)
        {
            $amountTotalPack = self::calculateRateForPack (
                $rf,
                $reservationPack,
                $reservation->getDateCheckIn(),
                $reservation->getDateCheckOut(),
                $reservation->getPropertyId(),
                $diffDays,
                $reservation->getReservationDate(),
                $defaultCutOff
            );
            // si la reserva esta siendo creada
            if(is_null($reservation->getState()))
                $reservationPack->setPrice($amountTotalPack);
            $totalChildren = $totalChildren + $reservationPack->getNumberKids();
            $totalAdults = $totalAdults + $reservationPack->getNumberAdults();
            $amountTotal = $amountTotal + $amountTotalPack;
        }

        // si la reserva esta siendo creada
        if (is_null($reservation->getState())) {
            $reservation
                ->setChildNumber($totalChildren)
                ->setAdultNumber($totalAdults);
        }

        if(!empty(self::$idSession)) {
            foreach (self::$idSession as $idSession) {
                CoreSession::renewSessionReservation($idSession);
            }
        }

        if (is_null($reservation->getState())) 
            $reservation->setTotalToPay($amountTotal);
        /*else
            if ($reservation->getTotalToPay() != $amountTotal)
                throw new NotAvailableException($reservation->getReservationDate()->format('d-m-Y'),'price_change');*/

        if (is_null($reservation->getState()) && self::ACTIVATE_TAX_REDUCTION && $amountTotal <= 200000 && $clientCondition && $reservation->getPropertyId()->getTaxRate() > 0) {
            
            $reservation->setTax(0.1);
            self::$recalculated = true;
        }
    }

    /**nuevo**/
    public static function calculateRateForPack (
        RepositoryFactoryInterface $rf,
        ReservationPack $rp,
        \DateTime $checkInDate,
        \DateTime $checkOutDate,
        Property $property,
        $diffDays,
        $reservationDate,
        $defaultCutOff = 0
    ) {

        //repositorio de Daily Pack y Daily Room
        $rpDailyPack = $rf->get('DailyPack');
        $rpDailyRoom = $rf->get('DailyRoom');

        //consultar los daily pack del entre las fechas especificadas
        $dailyPackages = $rpDailyPack->findByDatesRoomId(
            $rp->getPackId()->getId(),
            $checkInDate->format('Y-m-d'),
            $checkOutDate->format('Y-m-d')
        );
        $rp->setDailyPackages($dailyPackages);
        $dailyRooms = $rpDailyRoom->findByDatesRoomId(
            $rp->getPackId()->getRoom()->getId(),
            $checkInDate,
            $checkOutDate
        );
        $rp->setDailyRooms($dailyRooms);

        if(empty($dailyRooms))
            throw new NotAvailableException($checkInDate->format('d-m-Y'),'without_dr');

        if(empty($dailyPackages))
            throw new NotAvailableException($checkInDate->format('d-m-Y'),'without_dp');

        $totalPeopleReservationPack = $rp->getNumberKids() + $rp->getNumberAdults();

        //si la cantidad de personas de la reserva es mayor a la cantidad de personas admitidas en la habitación
        if ($totalPeopleReservationPack>$rp->getPackId()->getRoom()->getMaxPeople())
            throw new NotAvailableException($reservationDate,'max_number_people_exceeded');

        //si la cantidad de personas de la reserva es mayor a la cantidad de personas admitidas en la habitación
        if ($totalPeopleReservationPack<$rp->getPackId()->getRoom()->getMinPeople())
            throw new NotAvailableException($reservationDate,'min_number_people_exceeded');

        //validando la disponibilidad en los DailyRoom de la reserva seleccionada
        $currentDate = clone $checkInDate;
        foreach ($dailyRooms as $currentDaily) {
            self::validateDailyRoom(
                $currentDaily,
                $rp->getNumberRooms(),
                $checkInDate,
                $checkOutDate,
                $reservationDate,
                $currentDate,
                $defaultCutOff
            );
            $currentDate->modify('+1 day');
        }

        //se calcula el total del reservation pack sumando cada uno de los precios de los dailys obtenidos
        $baseAmountTotalDaily = 0;
        $currentDate = clone $checkInDate;
        foreach ($dailyPackages as $currentDaily) {
            self::validateDailyPack(
                $currentDaily,
                $rp->getNumberRooms(),
                $diffDays,
                $checkInDate,
                $checkOutDate,
                $currentDate
            );
            if ($currentDaily->getDate() != $checkOutDate) {
                $baseAmountTotalDaily = $baseAmountTotalDaily + $currentDaily->getSellRate();
            }
            $currentDate->modify('+1 day');
        }

        $amountTotalDaily = self::calculateClientRate ($baseAmountTotalDaily,$property);

        $room = $rp->getPackId()->getRoom();

        $numberPeople = ($room->getKidPayAsAdult()) ?
            $rp->getNumberAdults() + $rp->getNumberKids() :
            $rp->getNumberAdults();
        $numberKids = ($room->getKidPayAsAdult()) ?
            0 :
            $rp->getNumberKids();

        //aumento por persona
        $amountTotalDaily = $amountTotalDaily + self::getRateByPeople(
                $rp->getPackId()->getRoom(),
                $baseAmountTotalDaily,
                $numberPeople,
                $diffDays
            );

        //aumento por niño
        if ($numberKids>0) {
            $amountTotalDaily = $amountTotalDaily +
                self::getRateByKid(
                	$rp->getChildrenAge(),
                    $rp->getPackId()->getRoom(),
                    $baseAmountTotalDaily,
                    $numberKids,
                    $diffDays
                );
        }

        //aumento por politicas de cancelación seleccionada
        /*$amountTotalDaily = $amountTotalDaily +
            self::calculateCancellationPolice(
                $rp->getPropertyCancellationPolicyId(),
                $amountTotalDaily
            );*/

        //se multiplica por cantidad de pack solicitados
        $amountTotalDaily = $amountTotalDaily * $rp->getNumberRooms();

        $rp->setPrice($amountTotalDaily);

        return $amountTotalDaily;
    }

    /**
     * Calcula la Tarifa que se le debe mostrar al cliente en base a un precio dado y a las condiciones de tarifas del establesimiento
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 30-10-2015
     *
     * @param $price float
     * @param $property
     *
     * @return float
     */
    public static function calculateClientRate ($price,Property $property)
    {
        $sellPrice = $price;

        //si se cargo tarifa neta (esto se vuela por conveniencia)
        if ( $property->getRateType()==2 && !$property->getTax())
            $sellPrice = $sellPrice * (1+$property->getTaxRate());

        return round($sellPrice,4);
    }

    /**
     *  valida la conisistencia de los datos entre un da
     *ilyRoom y una reservacion
     *
     * @param DailyRoom $dr
     * @param $amountRoom
     * @param $checkInDate
     * @param $checkOutDate
     * @param $reservationDate
     * @param $currentDay
     * @param int $defaultCutOff
     * @throws NotAvailableException
     * @internal param int $minAvailabilityRoom
     * @return Boolean
     */
    private static function validateDailyRoom (
        DailyRoom $dr,
        $amountRoom,
        $checkInDate,
        $checkOutDate,
        $reservationDate,
        $currentDay,
        $defaultCutOff = 0
    ) {
        $reservationDate = isset($reservationDate) ?
            new \DateTime($reservationDate->format('d-m-Y')) :
            (new \DateTime())->setTime(0,0,0);
        $diffCutOff = $reservationDate->diff($dr->getDate())->days;
        $diffDays = $checkInDate->diff($checkOutDate)->days;
        if ($dr->getDate()->format('Y-m-d') != $checkOutDate->format('Y-m-d')) {
            //se busca la menor disponibilidad entre los DailyRoom
            if ($dr->getAvailability() - $dr->getLockeAvailability(self::$idSession) < $amountRoom) {
                throw new NotAvailableException($dr->getDate()->format('d-m-Y'), 'not_available_amount_room');
            } else if ($dr->getStopSell())
                throw new NotAvailableException($dr->getDate()->format('d-m-Y'),'stop_sell_dr');
            else if ($dr->getCutOff() > $diffCutOff || $defaultCutOff > $diffCutOff)
                throw new NotAvailableException($dr->getDate()->format('d-m-Y'),'cut_off_dr');
            else if (!($dr->getMinNight() <= $diffDays and $diffDays <= $dr->getMaxNight()))
                throw new NotAvailableException($dr->getDate()->format('d-m-Y'),'min_nigth_dr');
            else if ($currentDay->format('Y-m-d') != $dr->getDate()->format('Y-m-d'))
                throw new NotAvailableException($dr->getDate()->format('d-m-Y'),'not_exist_dr');
        }
    }

    /**
     *  valida la conisistencia de los datos entre un dailyPack y una reservacion
     *
     * @param DailyPack $dp
     * @param $amountRoom
     * @param Integer $diffDays
     * @param DateTime $checkInDate
     * @param DateTime $checkOutDate
     *
     * @param $currentDate
     * @throws NotAvailableException
     * @internal param int $minAvailabilityPack
     * @return Boolean
     */
    private static function validateDailyPack(
        DailyPack $dp,
        $amountRoom,
        $diffDays,
        $checkInDate,
        $checkOutDate,
        $currentDate
    ) {
        if ($dp->getDate()->format('Y-m-d') != $checkOutDate->format('Y-m-d')) {
            //se busca la menor disponibilidad entre los dailYpack
            if ($dp->getSpecificAvailability() - $dp->getLockeAvailability(self::$idSession) < $amountRoom)
                throw new NotAvailableException($dp->getDate()->format('d-m-Y'),'not_available_amount_pack');
            else if ($dp->getMinNight() > $diffDays)
                // Se verifica que el min/max de noches concuerden con los dias de reserva
                throw new NotAvailableException($dp->getDate()->format('d-m-Y'),'min_nigth_dp');
            else if ($diffDays > $dp->getMaxNight())
                // Se verifica que el min/max de noches concuerden con los dias de reserva
                throw new NotAvailableException($dp->getDate()->format('d-m-Y'),'max_nigth_dp');
            else if ($dp->getCloseOut())
                //Se verifica que el pack este disponible mediante CloseOut
                throw new NotAvailableException($dp->getDate()->format('d-m-Y'),'close_out_dp');
            else if ($dp->getDate()->format('Y-m-d') == $checkInDate->format('Y-m-d') and $dp->getClosedToArrival())
                //Se verifica que el pack este disponible para reservar el día de llegada
                throw new NotAvailableException($dp->getDate()->format('d-m-Y'),'closed_to_arrival_dp');
            else if ($currentDate->format('d-m-Y') != $dp->getDate()->format('d-m-Y'))
                //Se verifica que el pack corresponda a la fecha que corresponde evaluar
                throw new NotAvailableException($currentDate->format('d-m-Y'),'not_exist_dp');
        } else if ($dp->getDate()->format('Y-m-d') == $checkOutDate->format('Y-m-d') and $dp->getClosedToDeparture())
            //Se verifica que el pack este disponible para reservar el día de salida
            throw new NotAvailableException($dp->getDate()->format('d-m-Y'),'closed_to_departure');
    }

    /**
     * Función usada para el calculo del precio de venta
     * dada la politica de cancelación.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @param $cancellationPolicy   Politicas de Cancelación
     * @param $sellRate             Precio de Venta
     * @return float
     */
    public static function calculateCancellationPolice($cancellationPolicy, $sellRate)
    {
        switch ($cancellationPolicy->getVariationType()) {
            case 1:// Por Porcentaje.
                $sellRate = $sellRate * $cancellationPolicy->getVariationAmount();
                break;
            case 2:// Por Monto Fijo.
                $sellRate =$cancellationPolicy->getVariationAmount();
                break;
        }
        return $sellRate;
    }

    /**
     *  esta función obtiene el aumento por persona de una habitación en base a un precio y un numero de personas dados
     *
     * @author Gabriel Camacho <gcamacho@navicu.com>
     *
     * @param $roomEntity
     * @param $sellRate
     * @param $numberPeople
     * @return float
     */
    public static function getRateByPeople($roomEntity, $sellRate, $numberPeople, $diffDays,$round = false) {
        $response = self::calculateRateByPeople($roomEntity, $sellRate, $diffDays, $numberPeople,$round);
        if(!empty($response))
            return $response[0]["increase"];
        return 0;
    }

    /**
     * Función usada para el calculo del precio dado las caracteristicas
     * del establecimiento. Y de los requerimientosdel usuario.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @param $sellRate     Precio de Venta
     * @param $roomEntity   Objeto Habitación
     * @param array $data   Manejo de los parametros variationType, increase y dayCount
     * @return float
     */
    public static function calculateMainRates($sellRate, $roomEntity, $data)
    {
        $iva = $roomEntity->getProperty()->getTax() ? 1 : (1 + $roomEntity->getProperty()->getTaxRate());

        if ($data["variationType"] == 1) { // Incremento por Porcentaje
            $price = $data["increase"]->getAmountRate() * $sellRate;
        } else { // Incremento por Monto Fijo
            if ($roomEntity->getProperty()->getRateType() == 2) { //sellrate
                $price = $data["increase"]->getAmountRate() * $data["dayCount"];
            } else { //netRate
                $price = (($data["increase"]->getAmountRate() * $iva) / (1 - $roomEntity->getProperty()->getDiscountRate())) * $data["dayCount"];
            }
        }
        return $price;
    }

    /**
     * Función usada para el calculo del precio por persona
     * dada una habitación.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @param $roomEntity       Habitación
     * @param $sellRate         Precio de Venta
     * @return float
     */
    public static function calculateRateByPeople($roomEntity, $sellRate, $dayCount, $numberPeople = null, $round = true)
    {
        $data["variationType"] = $roomEntity->getVariationTypePeople();
        $data["dayCount"] = $dayCount;
        $ratesByPeople = $roomEntity->getRatesByPeoples();
        $property = $roomEntity->getProperty();
        $response = [];
        new RateExteriorCalculator(self::$rf, null, self::$request_date);

        if ($roomEntity->getIncrement()) {
            $increase = 0;
            for ($rp = 0; $rp < count($ratesByPeople); $rp++) {

                if (($property->getChild()==false && $roomEntity->getMinPeople() <= $ratesByPeople[$rp]->getNumberPeople()) or $property->getChild()) {
                    $data["increase"] = $ratesByPeople[$rp];
                    $pricePerPerson = self::calculateMainRates($sellRate, $roomEntity, $data);

                    $sellRate = $sellRate + $pricePerPerson;
                    $increase = $increase + $pricePerPerson;
                    $increaseIva = self::calculateClientRate(
                        $increase,
                        $roomEntity->getProperty()
                    );
                    $priceIva = self::calculateClientRate(
                        $sellRate,
                        $roomEntity->getProperty()
                    );
                    if ($numberPeople == $ratesByPeople[$rp]->getNumberPeople() || is_null($numberPeople)) {
                        $response[] = [
                            "amountPeople" => $ratesByPeople[$rp]->getNumberPeople(),
                            "price" => RateExteriorCalculator::calculateRateChange($priceIva),
                            "increase" => $round ? round($increaseIva) : $increaseIva
                        ];
                    }
                }
            }
        } else {
            for ($rp = 1; $rp <= $roomEntity->getMaxPeople(); $rp++) {
                if (($property->getChild()==false && $roomEntity->getMinPeople() <= $rp) or $property->getChild()) {
                    $priceIva = self::calculateClientRate(
                        $sellRate,
                        $roomEntity->getProperty()
                    );
                    $response[] = [
                        "amountPeople" => $rp,
                        "price" => RateExteriorCalculator::calculateRateChange($priceIva),
                        "increase" => 0
                    ];
                }
            }
        }
        return $response;
    }

    /**
     * esta función obtiene el aumento por niños para una habitacion en base a un precio base y una cantidad de niños
     *
     * @author Gabriel Camacho <gcamacho@navicu.com>
     *
     * @param $roomEntity
     * @param $sellRate
     * @param $numberKid
     * @return float
     */
    public static function getRateByKid($childrenAge, $roomEntity, $sellRate, $numberKid,$diffDays, $round = false) {
        $response = self::calculateRateByKid($roomEntity, $sellRate, $diffDays, $numberKid, $round);
        if(!empty($response)) {
        	$total = 0;
	        foreach ($childrenAge as $currenChild) {
		        foreach ($response as $currentAgeRange) {
		        	if(isset($currentAgeRange['ageRange'])) {
				        if ( $currenChild->getAge() >= $currentAgeRange['ageRange'][0] &&
				             $currenChild->getAge() <= $currentAgeRange['ageRange'][1]
				        ) {
					        $total = $total + $currentAgeRange[0]["increase"];
				        }
			        }
		        }
	        }

	        return $total;
        }

        return 0;
    }

    /**
     * Función usada para el calculo del precio por niño
     * dada una habitación.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @param $roomEntity       Habitación
     * @param $sellRate         Precio de Venta
     * @return float
     */
    public static function calculateRateByKid($roomEntity, $sellRate, $dayCount, $numberkid = null, $round = true)
    {
        $response = [];
        new RateExteriorCalculator(self::$rf, null, self::$request_date);

        if ($roomEntity->getKidPayAsAdult()) {
            $rateByPeople = self::calculateRateByPeople($roomEntity, $sellRate, $dayCount);
            $rateByKids = [];
            for ($i=1; $i<count($rateByPeople); $i++) {
                $rateByKids[] = [
                    "amountkid" => $rateByPeople[$i]['amountPeople']-1,
                    "price" => RateExteriorCalculator::calculateRateChange($rateByPeople[$i]['price']),
                    "increase" => $rateByPeople[$i]['increase'],
                ];
            }
            return $rateByKids;
        }

        if ($roomEntity->getIncrementKid()) {
            $variationTypes = $roomEntity->getVariationTypeKids();
            $data["dayCount"] = $dayCount;
            $ratesByKid = $roomEntity->getRatesByKids();
            $auxSellRate = 0;
            $increase = 0;
	        $property = $roomEntity->getProperty();
	        $propertyAgeRanges = $property->getAgePolicy()['child'];
            foreach ($propertyAgeRanges as $Currentrange) {
                array_push($response, array("ageRange" => $Currentrange));
            }
            for ($rk = 0; $rk < count($ratesByKid); $rk++) {

                $data["increase"] = $ratesByKid[$rk];
                $data["variationType"] = $variationTypes[$ratesByKid[$rk]->getIndex()];
                $pricePerkid = self::calculateMainRates($sellRate, $roomEntity, $data);

                $sellRate = $pricePerkid + $auxSellRate;
                $auxSellRate = $sellRate;
                $increase = $increase + $pricePerkid;
                $priceIva = self::calculateClientRate (
                    $sellRate,
                    $roomEntity->getProperty()
                );

                $increaseIva = self::calculateClientRate (
                    $increase,
                    $roomEntity->getProperty()
                );

                if ($numberkid == $ratesByKid[$rk]->getNumberKid() or is_null($numberkid)) {
                    array_push(
                        $response[$ratesByKid[$rk]->getIndex()],
                        array(
                            "amountkid" => $ratesByKid[$rk]->getNumberKid(),
                            "price" => RateExteriorCalculator::calculateRateChange($priceIva),
                            "increase" => $round ? round($increaseIva) : $increaseIva)
                        );

//                    $response[] = [
//                        "amountkid" => $ratesByKid[$rk]->getNumberKid(),
//                        "price" => RateExteriorCalculator::calculateRateChange($priceIva),
//                        "increase" => $round ? round($increaseIva) : $increaseIva
//                    ];
                }
            }
        } else {
            for ($rk = 1; $rk <= $roomEntity->getMaxPeople() - 1; $rk++) {
                $response[] = [
                    "amountkid" => $rk,
                    "price" => 0,
                    "increase" => 0
                ];
            }
        }

        return $response;
    }
}