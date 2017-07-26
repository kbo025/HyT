<?php

namespace Navicu\Rest\ApiBundle\Controller\Extranet;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Navicu\Core\Application\UseCases\PropertyInventory\Grid\InventoryData\InventoryDataCommand;
use Navicu\Core\Application\UseCases\PropertyInventory\Grid\DailyUpdate\DailyUpdateCommand;

use Navicu\Core\Application\UseCases\PropertyInventory\MassLoad\SetDataMassLoad\SetDataMassLoadCommand;
use Navicu\Core\Application\UseCases\PropertyInventory\MassLoad\GetDataMassLoad\GetDataMassLoadCommand;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Swagger\Annotations;

class InventoryController extends Controller
{

    /**
     * Lectura de la información de identificación  de los tipos de habitaciones ofrecidos por un establecimiento y sus servicios asociados   
     *
     * ### Respuesta Ok (e.g.) ###
     *
     *     {
     *       "success": true,
     *       "msg":"Ok",
     *       "data":{
     *         "rooms":[
     *           {
     *             "idRoom":"id-room-1",
     *             "packages":[
     *               {"idPack":"id-room-1-pack-1"},
     *               {"idPack":"id-room-1-pack-2"},
     *               {"idPack":"id-room-1-pack-3"}
     *             ]
     *           },
     *           {
     *             "idRoom":"id-room-2",
     *             "packages":[
     *               {"idPack":"id-room-2-pack-1"},
     *               {"idPack":"id-room-2-pack-2"},
     *               {"idPack":"id-room-2-pack-3"}
     *             ]
     *           }
     *         ]
     *       }
     *     }
     *
     * @ApiDoc(
     *  resource=false,
     *  description="Obtiene los identificadores de habitaciones y servicios del establecimiento",
     *  tags={
     *    "BETA" = "#FF6F00"
     *  },
     *  requirements={
     *      {
     *          "name"="slug",
     *          "dataType"="string",
     *          "description"="identificador del hotel (eg. 'hotel-example')"
     *      }
     *  },
     *  headers={
     *      {
     *          "name"="Authorization",
     *          "required"=true,
     *          "description"="Token de acceso otorgado para conectarse a la api navicu (eg. Authorization: Bearer N2Q5NjJiYjFishsh22838232gfñk43HNUF982MYjNdsa2236dh7iNzYzYTAwYTg4ZWNl )"
     *      }
     *  },
     *  statusCodes={
     *    200="Lectura Correcta",
     *    401="No authorizado",
     *    403="No authorizado para acceder al recurso",
     *    404="Slug del hotel incorrecto (property_not_found)"
     *  }
     * )
     */
    public function getMassLoadAction($slug,Request $request)
    {
        $data = json_decode($request->getContent(),true);
        
        if (!$this->haveAccess($slug))
            return new JsonResponse(['success' => false,'msg' => 'forbidden'],403);

        $user = $this->getUserOwnerBySlug($slug);
        if(is_null($user))
            return new JsonResponse(['success' => false, 'msg' => 'property_not_found'],404);

        $command = new GetDataMassLoadCommand($slug,$user,true);
        $response = $this->get('CommandBus')->execute($command);
        return new JsonResponse([
                'success' => $response->isOk(),
                'msg' => $response->getMessage(),
                'data' => $response->getData()
            ],
            $response->getStatusCode()
        );
    }

    /**
     * La carga masiva es el metodo por el cual podras cargar inventario en tu establecimiento en varias fechas, tipos de habitaciones y tipos de servicios en una sola petición  
     *
     * ### Parametros (e.g.) ###
     *
     *     {
     *       "dates":[
     *         "2017-06-13",
     *         "2017-06-14",
     *         "2017-06-15"
     *       ],
     *       "rooms":[
     *         {
     *           "idRoom":"id-room-1",
     *           "data":{
     *             "availability":10,
     *             "minNight":1,
     *             "maxNight":100,
     *             "cutOff":1,
     *             "stopSell":true
     *           },
     *           "packages":[
     *             {
     *               "specificAvailability":10,
     *               "minNight":1,
     *               "maxNight":100,
     *               "price":1000,
     *               "closeOut":true,
     *               "closedToArrival":true,
     *               "closedToDeparture":true,
     *               "idPack":"id-room-1-pack-1"
     *             }
     *           ]
     *         }
     *       ]
     *     }
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Carga de tarifas simultanea en habitaciones y servicios asociados",
     *  requirements={
     *      {
     *          "name"="slug",
     *          "dataType"="string",
     *          "description"="identificador del hotel (eg. 'hotel-example')"
     *      }
     *  },
     *  tags={
     *    "BETA" = "#FF6F00"
     *  },
     *  headers={
     *      {
     *          "name"="Authorization",
     *          "required"=true,
     *          "description"="Token de acceso otorgado para conectarse a la api navicu (eg. Authorization: Bearer N2Q5NjJiYjFishsh22838232gfñk43HNUF982MYjNdsa2236dh7iNzYzYTAwYTg4ZWNl )"
     *      }
     *  },
     *  parameters={
     *    {"name"="dates", "dataType"="Array", "required"=true, "description"="conjunto de fechas para cargar disponibilidad en formato yyyy-mm-dd", "format":"yyyy-mm-dd"},
     *    {"name"="rooms", "dataType"="Array", "required"=true, "description"="conjunto de habitaciones en las cuales se realizará la carga/actualización de datos"},
     *    {"name"="idRoom", "dataType"="String", "required"=true, "description"="identificador publico de la habitacion"},
     *    {"name"="data", "dataType"="Object", "required"=true, "description"="conjunto de valores para cargar/actualizar parametros de venta de una habitación"},
     *    {"name"="availability", "dataType"="Integer", "required"=false, "description"="Disponibilidad general de la habitación"},
     *    {"name"="minNight", "dataType"="Integer", "required"=false, "description"="Minimo de noches permitido para la venta de una habitación/servicio"},
     *    {"name"="maxNight", "dataType"="Integer", "required"=false, "description"="Maximo de noches permitido para la venta de una habitación/servicio"},
     *    {"name"="cutOff", "dataType"="Integer", "required"=false, "description"="Antelación requerida para la venta de una habitación"},
     *    {"name"="stopSell", "dataType"="Boolean", "required"=false, "description"="Indica si se detiene la venta para la habitación"},
     *    {"name"="packages", "dataType"="Array", "required"=false, "description"="Array de servicios disponibles para la habitación con el conjunto de datos a cargar/actualizar para ese servicio"},
     *    {"name"="specificAvailability", "dataType"="Integer", "required"=false, "description"="Disponibilidad especifica de habitación + servicio, debe estar dentro de los parametros de la habitación"},
     *    {"name"="price", "dataType"="Float", "required"=false, "description"="Costo de la habitación + servicio"},
     *    {"name"="closeOut", "dataType"="Boolean", "required"=false, "description"="Indica si se detienen las ventas para la habitación + servicio"},
     *    {"name"="closedToArrival", "dataType"="Boolean", "required"=false, "description"="Indica si se detienen las ventas de habitación + servicio para las fechas como fecha de checkin"},
     *    {"name"="closedToDeparture", "dataType"="Boolean", "required"=false, "description"="Indica si se detienen las ventas de habitación + servicio para las fechas como fecha de checkout"},
     *    {"name"="idPack", "dataType"="String", "required"=true, "description"="identificador publico de la habitación mas el servicio"}
     *  },
     *  statusCodes={
     *    201="Ejecutado con exito",
     *    401="No authorizado",
     *    403="No authorizado para acceder al recurso",
     *    400="Error de validacion, revise los datos enviados e intente de nuevo"
     *  }
     * )
     */
    public function postMassLoadAction($slug,Request $request)
    {
        $data = json_decode($request->getContent(),true);
        
        if (!$this->haveAccess($slug))
            return new JsonResponse(['success' => false,'msg' => 'forbidden'],403);
        
        $user = $this->getUserOwnerBySlug($slug);
        if(is_null($user))
            return new JsonResponse(['success' => false, 'msg' => 'property_not_found'],404);

        $command = new SetDataMassLoadCommand($slug,$user,$data,true);

        $response = $this->get('CommandBus')->execute($command);
        return new JsonResponse([
                'success' => $response->isOk(),
                'msg' => $response->getMessage()
            ],
            $response->getStatusCode()
        );
    }


    /**
     * Carga o modifica las condiciones para la venta de una habitación o servicio para una fecha especifica
     *
     * ### Parametros (e.g. Actulización de habitación) ###
     *
     *     {
     *     	  "idRoom":"id-room",
     *     	  "date":"2017-06-12",
     *     	  "minNight":5,
     *     	  "maxNight":70,
     *     	  "availability":30,
     *     	  "cutOff":5,
     *     	  "stopSell":false
     *     }
     *
     *
     * ### Parametros (e.g. Actulización de servicio) ###
     *
     *     {
     *         "idPack":"id-pack",
     *         "date":"2017-06-22",
     *         "price":1000,
     *         "minNight":1,
     *         "maxNight":100,
     *         "specificAvailability":5,
     *         "closeOut":true,
     *         "closedToDeparture":true,
     *         "closedToArrival":true
     *     }
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Carga de condiciones de venta de una habitación o servicio en una fecha especifica",
     *  requirements={
     *      {
     *          "name"="slug",
     *          "dataType"="string",
     *          "description"="identificador del hotel (eg. 'hotel-example')"
     *      }
     *  },
     *  tags={
     *    "BETA" = "#FF6F00"
     *  },
     *  headers={
     *      {
     *          "name"="Authorization",
     *          "required"=true,
     *          "description"="Token de acceso otorgado para conectarse a la api navicu (eg. Authorization: Bearer N2Q5NjJiYjFishsh22838232gfñk43HNUF982MYjNdsa2236dh7iNzYzYTAwYTg4ZWNl )"
     *      }
     *  },
     *  parameters={
     *    {"name"="idRoom", "dataType"="String", "required"=true, "description"="identificador publico de la habitacion (solo es necesario para cargar/actualizar datos de una habitación)"},
     *    {"name"="idPack", "dataType"="String", "required"=true, "description"="identificador publico de la habitación mas el servicio (solo es necesario para cargar/actualizar datos de un servicio)"},
     *    {"name"="date", "dataType"="String", "required"=true, "format":"yyyy-mm-dd", "description"="Fecha en la que se cargará/actualizará los datos de la habitación/servicio"},
     *    {"name"="availability", "dataType"="Integer", "required"=false, "description"="Disponibilidad general de la habitación"},
     *    {"name"="minNight", "dataType"="Integer", "required"=false, "description"="Minimo de noches permitido para la venta de una habitación/servicio"},
     *    {"name"="maxNight", "dataType"="Integer", "required"=false, "description"="Maximo de noches permitido para la venta de una habitación/servicio"},
     *    {"name"="cutOff", "dataType"="Integer", "required"=false, "description"="Antelación requerida para la venta de una habitación"},
     *    {"name"="stopSell", "dataType"="Boolean", "required"=false, "description"="Indica si se detiene la venta para la habitación"},
     *    {"name"="specificAvailability", "dataType"="Integer", "required"=false, "description"="Disponibilidad especifica de habitación + servicio, debe estar dentro de los parametros de la habitación"},
     *    {"name"="price", "dataType"="Float", "required"=false, "description"="Costo de la habitación + servicio"},
     *    {"name"="closeOut", "dataType"="Boolean", "required"=false, "description"="Indica si se detienen las ventas para la habitación + servicio"},
     *    {"name"="closedToArrival", "dataType"="Boolean", "required"=false, "description"="Indica si se detienen las ventas de habitación + servicio para las fechas como fecha de checkin"},
     *    {"name"="closedToDeparture", "dataType"="Boolean", "required"=false, "description"="Indica si se detienen las ventas de habitación + servicio para las fechas como fecha de checkout"}
     *  },
     *  statusCodes={
     *    201="Ejecutado con exito",
     *    401="No authorizado",
     *    403="No authorizado para acceder al recurso",
     *    400="Error de validacion, revise los datos enviados e intente de nuevo",
     *  }
     * )
     */
    public function postDailyAction($slug, Request $request)
    {
        $data = json_decode($request->getContent(),true);

        if (!$this->haveAccess($slug))
            return new JsonResponse(['success' => false,'msg' => 'forbidden'],403);

        $user = $this->getUserOwnerBySlug($slug);
        if(is_null($user))
            return new JsonResponse(['success' => false, 'msg' => 'property_not_found'],404);

        $command = new DailyUpdateCommand([
            'id' => null,
            'data' => $data,
            'userSession' => $user,
            'apiRequest' => true
        ]);


        $response = $this->get('CommandBus')->execute($command);
        return new JsonResponse([
                'success' => $response->isOk(),
                'msg' => $response->getMessage(),
                'data' => $response->getData()
            ],
            $response->getStatusCode()
        );
    }

    private function getUserOwnerBySlug($slug)
    {
        $rep = $this->getDoctrine()->getRepository('NavicuDomain:Property');
        $property = $rep->findOneBy(['slug' => $slug]);
        if ($property) {
            $users = $property->getOwnersProfiles();
            return $users[0];
        }
        return null; 
    }

    private function haveAccess($slug)
    {
        $tokenManager = $this->container->get('fos_oauth_server.access_token_manager.default');
        $token = $this->container->get('security.context')->getToken()->getToken(); 
        $accessToken = $tokenManager->findTokenByToken($token);
        $owner = $accessToken->getUser()->getOwnerProfile();

        $i = 0;
        $access = false;
        if (!is_null($owner)) {
            $properties = $owner->getProperties();
            while($i < count($properties) && !$access) {
                $access = ($properties[$i]->getSlug() === $slug);
                $i++;
            }
        }
        return $access;
    }
}
