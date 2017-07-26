<?php
namespace Navicu\Core\Application\UseCases\AAVV\Reservation\GetListReservation;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreSession;

/**
 * Comando para buscar un listado de reservas dado un conjunto
 * de parametros
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class GetListReservationHandler implements Handler
{
    private $rf;

    /**
     * Metodo que implementa y ejecuta el conjunto de acciones que completan
     * el caso de uso Registrar Usuario Agencia de Viaje
     *
     *  @param SetDataReservationCommand $command
     *  @param RepositoryFactory $rf
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf = null)
    {
        //Asignación de valores por defecto
        $request = $command->getRequest();

        $reservationRP = $rf->get("Reservation");
        $request["select"] = null;

        $aavv = $rf->get('AAVV')->findOneByArray(["slug" => $request["slug"]]);
        // Busqueda del objeto AAVV sea por session de usuario o por
        if (CoreSession::havePermissons('admin_aavv_affiliate_details','reservation_details')){

            if(!$aavv)
                return new ResponseCommandBus(400, '', null);
            $request["where"] = "aavv_id = ".$aavv->getId();

        } else
            $request["where"] = "aavv_id = ".$request["user"]->getAavvProfile()->getAAVV()->getId();

        if (is_null($request["status"])) {
            $request["where"] .= " and status is not null";
        } else if ($request["status"]==0) {
            $request["where"] .= " and status = 0 ";
        } else if ($request["status"]==1) {
            $request["where"] .= " and status = 1 ";
        } else if ($request["status"]==2) {
            $request["where"] .= " and status = 2 ";
        } else if ($request["status"]==3) {
            $request["where"] .= " and status = 3 ";
        } else {
            $request["where"] .= " and status is not null";
        }

        if ($request["startDate"]) {
            switch ($request["dateType"]) {
                case 1:
                default:
                    $dateType = "create_date";
                    break;
                case 2:
                    $dateType = "check_in";
                    break;
                case 3:
                    $dateType = "check_out";
                    break;
            }

            $request["where"] .= " and $dateType >='".$request["startDate"]."' and $dateType <='".$request["endDate"]."'";
        }

        $reservations = $reservationRP->findReservationByWords($request);

        $response["properties"] = [];
        foreach ($reservations["data"] as $data) {
            $aux["id"] = $data["id"];
            $aux["publicid"] = $data["public_id"];
            $aux["createdate"] = $data["create_date"];
            $aux["checkin"] = $data["check_in"];
            $aux["checkout"] = $data["check_out"];
            $aux["fullname"] = $data["client_name"];
            $aux["price"] = round($data["total_to_pay"] * (1-$aavv->getAgreement()->getDiscountRate()),2);
            $aux["status"] = $data["status"];
            $aux["property"] = $data["name_property"];
            $aux["city"] = $data["city"];
            array_push($response["properties"], $aux);
        }
        $response["page"] = $reservations["pagination"];
        return new ResponseCommandBus(200, 'Ok', $response);

    }
}
