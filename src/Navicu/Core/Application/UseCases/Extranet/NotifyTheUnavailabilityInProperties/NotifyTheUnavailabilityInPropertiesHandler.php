<?php
/**
 * Created by PhpStorm.
 * User: Isabel Nieto
 * Date: 11/05/16
 * Time: 12:48 PM
 */

namespace Navicu\Core\Application\UseCases\Extranet\NotifyTheUnavailabilityInProperties;

use Monolog\Handler\IFTTTHandler;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Adapter\CoreTranslator;
use Symfony\Component\Validator\Constraints\Date;

/**
 * Clase para ejecutar el caso de uso NotifyTheUnavailabilityInProperties
 * @author Currently Working: Isabel Nieto <isabelcnd@gmail.com>
 * @version 06/06/2016
 */

class NotifyTheUnavailabilityInPropertiesHandler implements Handler
{
    /**
     *   instancia del repositoryFactory
     *   @var RepositoryFactory $rf
     */
    protected $rf;
    /**
     *  Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $this->rf = $rf;
        $request = $command->getRequest();

        // Para la seccion de admin
        if (!isset($request['slug']))
            $response = $this->getStructureProperties($request);
        else { //Para la seccion de hotelero
            $property = $rf->get('Property')->findBySlug($request["slug"]);

            $response = $this->buildStructure($property);

            $response['slug'] = $request['slug'];
            $response['property'] = $property->getName();
            return new ResponseCommandBus(200, 'ok', $response);
        }

        return new ResponseCommandBus(200, 'ok', $response);
    }

    /**
     * Construye la estructura de respuesta de los establecimientos
     * sin disponibilidades
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 29/08/2016
     * @param request conjunto de datos enviado en el command
     * @return array
     */
    private function getStructureProperties($request)
    {
        $rpProperty = $this->rf->get('Property');
        $searchVector = "search_vector";

        if (CoreSession::isRole('ROLE_SALES_EXEC') and !CoreSession::isRole('ROLE_ADMIN')) {
            $request['search'] = $request['search']." ".$request['user']->getNvcProfile()->getFullName();
            $arrayOfProperties = $rpProperty->findWithoutAvailabilyWithFilter($request, $searchVector);
        }
        else
            $arrayOfProperties = $rpProperty->findWithoutAvailabilyWithFilter($request, $searchVector);

        $response['properties'] = $arrayOfProperties['data'];
        $response['pagination'] = $arrayOfProperties['pagination'];

        $length = count($response['properties']);
        for ($ii = 0; $ii < $length; $ii++)
            $response['properties'][$ii]['date'] = $request['startDate']->format('d-m-Y');

        return $response;
    }

    /**
     * Retorna los datos de las habitaciones y serviciones
     * que no tienen disponibilidad ni tarifas cargas
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $slug
     * @return array
     * @version 29/08/2016
     */
    private function getStructureBySlug($slug)
    {
        return $response = [];
    }

    /**
     * Funcion encargada de devolver los 90 dias siguientes a las fecha actual
     *
     * @author Currently Working: Isabel Nieto <isabelcnd@gmail.com>
     * @version 06/06/2016
     *
     * @return array 90 dias siguientes luego de la fechaa ctual
     */
    public function createArrayOfDays() {
        $arrayOfDays = [];
        $today = new \DateTime("now 00:00:00");
        $oneMonthLater = new \DateTime("now 00:00:00");
        $interval = new \DateInterval('P1M');

        $oneMonthLater->add($interval);
        $maxDays = $today->diff($oneMonthLater)->days;
        $today = $today->format('Y-m-d');

        for ($ii = 0; $ii < $maxDays; $ii++) {
            $date = strtotime("$today +$ii days");
            $date = new \DateTime(date('Y-m-d', $date));
            array_push($arrayOfDays, $date->format('d-m-Y'));
        }
        return $arrayOfDays;
    }

    /**
     * Funcion que se encarga de filtrar los datos del arreglo de objetos de fechas, a un arreglo por fechas y
     * transformarlo a un string
     *
     * @author Currently Working: Isabel Nieto <isabelcnd@gmail.com>
     * @version 06/06/2016
     *
     * @param array $dailyDates arreglo obtenido de la base de datos de los DR o DP con disponibilidad.
     * @return mixed un arreglo de fechas formateado
     */
    public function getDates($dailyDates) {
        return array_map(function ($var) {
            return date('d-m-Y', $var);
        }, array_column(
                $dailyDates,
                "date")
        );
    }

    /**
     * Funcion para buscar y armar la estructura de los dailyRoom desde la primera fecha encontrada que no existe
     * tarifa asignada.
     *
     * @author Currently Working: Isabel Nieto <isabelcnd@gmail.com>
     * @version 02/08/2016
     *
     * @param object $property entidad property
     * @return array
     */
    public function buildStructure($property)
    {
        $rpSearchEngine = $this->rf->get('SERepository');
        $rooms = $property->getRooms();
        $today = new \DateTime("now 00:00:00");
        $oneMonthLater = new \DateTime("now 00:00:00");
        $interval = new \DateInterval('P1M');
        $oneMonthLater->add($interval);
        $maxDays = $today->diff($oneMonthLater)->days;

        $arrayOfDaysComplete = $this->createArrayOfDays();

        $data["startDate"] = strtotime(date("Y-m-d"));
        $data["endDate"] = strtotime ( '+31 day' , strtotime ( date("Y-m-d") ) );
        $response['listPorperty'] = true;
        $result = [];

        for ($jj = 0; $jj < count($rooms); $jj++) {
            if ($rooms[$jj]->getIsActive()) {
                $dailyRoomDates = $rpSearchEngine->resultDailyRoomByDateRank($data, $rooms[$jj]->getId());
                $datesDr = $this->getDates($dailyRoomDates);
                $dateDiff = array_diff($arrayOfDaysComplete, $datesDr);
                $lengthDate = count($arrayOfDaysComplete);
                $foundExistingDate = false;
                for ($ii = 0; $ii < $lengthDate; $ii++){
                    if (!empty($dateDiff[$ii])) {
                        // Buscamos si dentro de las fechas obtenidas existen dr por cargar
                        $currentDate = new \DateTime("$dateDiff[$ii] 00:00:00");
                        // Si la fecha actual es menor a la fecha dentro de un mes entonces proseguimos
                        if ( ($ii < $maxDays) && ($currentDate->diff($oneMonthLater)->invert == 0) && ($currentDate->diff($oneMonthLater)->d < $maxDays)) {
                            $dateFormat = $currentDate->format('d-m-Y');
                            if (empty($roomObjDR[$dateFormat]))
                                $roomObjDR[$dateFormat] = [];
                            $newRoomObj['typeRoom'] = $rooms[$jj]->getName();
                            array_push($roomObjDR[$dateFormat], $newRoomObj);

                            //Obtener los dailyPack de ese DailyRoom inexistente
                            $packs = $rooms[$jj]->getPackages();
                            $lengthPack = count($packs);
                            for ($kk = 0; $kk < $lengthPack; $kk++) {
                                if (empty($roomObjDP[$dateFormat]))
                                    $roomObjDP[$dateFormat] = [];
                                $newPackObj['typeRoom'] = $rooms[$jj]->getName();
                                $newPackObj['pack'] = CoreTranslator::getTranslator($packs[$kk]->getType()->getCode());
                                array_push($roomObjDP[$dateFormat], $newPackObj);
                            }
                            $foundExistingDate = true;
                        }
                    }
                }
                // Si la habitacion tuvo por lo menos un dailyRoom creado correctamente, ese alojamiento
                // no debe ser incluido en el listado de admin
                if (!$foundExistingDate)
                    $response['listPorperty'] = false;
            }
        }

        if (isset($roomObjDR)) {
            foreach ($roomObjDR as $key => $value) {
                $obj['date'] = $key;
                $obj['rooms'] = $value;
                array_push($result, $obj);
            }
        }
        $response['dailyRoom'] = $result;

        $result = [];
        if (isset($roomObjDP)) {
            foreach ($roomObjDP as $key => $value) {
                $obj['date'] = $key;
                $obj['rooms'] = $value;
                array_push($result, $obj);
            }
        }
        $response['dailyPack'] = $result;
        return $response;
    }
}