<?php
/**
 * Created by Isabel Nieto <isabelcnd@gmail.com>.
 * Date: 21/07/16
 * Time: 09:27 AM
 */

namespace Navicu\Core\Application\UseCases\Admin\CurrencyExchange\CurrencyExchangeCalendar\GetCurrencyExchangeCalendar;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;

class GetCurrencyExchangeCalendarHandler implements Handler
{
    /**
     *  Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $exchangeRateRf = $rf->get("ExchangeRateHistory");

        $request = $command->getRequest();
        try {
            $dateRequest = isset($request['data']['request_date']) ? $request['data']['request_date'] : "now";

            $dateToConsult = new \DateTime("$dateRequest 00:00:00");
            $firstDate = $this->buildDateFromStart($dateToConsult);
            $firstDateFormat = $firstDate->format('Y-m-d');

            $secondDate = new \DateTime($firstDateFormat);
            $interval = new \DateInterval('P2M');
            $secondDate->add($interval);

            // Obtenemos la primera fecha que se tiene en el registro de la base de datos
            $iniDate = $exchangeRateRf->getFirstDate();

            // Si esta dentro del rango de fechas almacenadas en la base de datos
            if ($iniDate) {
                $data = $exchangeRateRf->getByIniDate($firstDate, $secondDate);
                $structure = $this->buildStructure($data, $iniDate[0]->getDate());
                $response = new ResponseCommandBus(200, 'Ok', $structure);
            }
            else
                $response = new ResponseCommandBus(400, "Bad Request", 'Bad Date');
        }
        catch (\Exception $e) {
            $response = new ResponseCommandBus(500, "\n".$e->getMessage()."\nLine: ".$e->getLine());
        }
        return $response;
    }

    /**
     * Funcion que genera la estructura para devovler un conjunto de dias con su porcentaje navicu, la
     * primera entrada realizada (fecha) de la base de datos y su tasa de cambio.
     *
     * @param $data
     * @param $firstDate, primera fecha encontrada en la base de datos
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 21/07/2016
     * @return array con los parametros definidos
     */
    public function buildStructure($data, $firstDate)
    {
        $response = [];
        $objDate = [];
        $response['calendar'] = [];
        $lastValueFormApi = 0;

        $response['first_date'] = $firstDate->format("d-m-Y");
        if (count($data) > 0) {
            foreach ($data as $row) {
                $objDate['date'] = $row->getDate()->format("d-m-Y");
                $objDate['percentage_navicu'] = $row->getPercentageNavicu();
                $objDate['sicad_2'] = $row->getDicom();
                array_push($response['calendar'], $objDate);
                if ($row->getRateApi() != 0)
                    $lastValueFormApi = $row->getRateApi();
            }
        }
        $response['rate_api'] = $lastValueFormApi;
        return $response;
    }

    /**
     * Funcion encargada de generar la fecha a devolver desde el inicio del mes ingresado
     *
     * @param object $dateToConsult, fecha en la que se hace la consulta
     * @return \DateTime
     */
    public function buildDateFromStart($dateToConsult)
    {
        // Si la fecha ingresada no es inicio de mes entonces se devuelve la creada con "now"
        if ($dateToConsult->format('d') > 1) {
            $day = 1;
            $month = $dateToConsult->format('m');
            $year = $dateToConsult->format('Y');

            $newDate = $day . "-" . $month . "-" . $year;
            $date = new \DateTime("$newDate 00:00:00");
        } else
            $date = $dateToConsult;

        return $date;
    }
}