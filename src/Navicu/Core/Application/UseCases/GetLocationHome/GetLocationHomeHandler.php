<?php

namespace Navicu\Core\Application\UseCases\GetLocationHome;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Model\ValueObject\Slug;

/**
 * Clase para ejecutar el caso de uso GetLocationHome
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 * @version 30/06/2015
 */

class GetLocationHomeHandler implements Handler
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
        $property = $rf->get('Property');
        $location = $rf->get('Location');

        $response = array();
        $properties = $property->findAll();

        $destinies = array();
        for ($p = 0; $p < count($properties); $p++) {

            if ($properties[$p]->getName() != 'Hotel Valladolid') {
                if ($properties[$p]->getLocation()->getCityId()) {
                    $id = $properties[$p]->getLocation()->getCityId()->getId();
                } else if  ($this->checkSpecialLocation($properties[$p]->getLocation()->getParent()->getSlug())) {
                    $id = $properties[$p]->getLocation()->getParent()->getId();
                } else if ($this->checkSpecialLocation($properties[$p]->getLocation()->getSlug())) {
                    $id = $properties[$p]->getLocation()->getId();
                } else {
                    $id = $properties[$p]->getLocation()->getParent()->getParent()->getId();
                }
                array_push($destinies, $id);
            }
        }

        $destinies = array_unique($destinies);
        $keyDestiny = array_keys($destinies);
        $auxDestiny = array();

        for ($d = 0; $d < count($destinies); $d++) {

            $destiny = $location->find($destinies[$keyDestiny[$d]]);


            if ($this->checkSpecialLocation($destiny->getSlug()))
                $name = $this->checkSpecialLocation($destiny->getSlug());
            else
                $name = $destiny->getTitle();


            $auxLocation['slug'] = $destiny->getSlug();
            $auxLocation['name'] = $name;
            $auxLocation['description'] = $this->getDescription($name);
            $auxLocation['countryCode'] = $destiny->getRoot()->getAlfa2();
            $auxLocation['type'] = $destiny->checkType();
            $auxLocation['path'] = $this->getPathImageCity($name);
            $auxLocation['coordenate'] = json_decode($this->getCoordenate($destiny->getSlug()));

            $order[$keyDestiny[$d]] = $auxLocation['name'];
            array_push($auxDestiny, $auxLocation);
        }
        array_multisort($order, SORT_ASC, $auxDestiny);

        return new ResponseCommandBus(200, 'Ok', $auxDestiny);
    }

    /**
     * La siguiente función retorna la descripcion de una ciudad
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $city
     * @return null
     * @version 25/11/2015
     */
    private function getDescription(&$city)
    {
        $response = null;
        switch ($city) {
            case 'Barquisimeto':
                $response = 'Vive tus carnavales en la capital musical';
                break;
            case 'Barinas':
                $response = 'El llano te recibirá con todo su esplendor';
                break;
            case 'Caracas':
                $response = 'Una ciudad con hermosos contrastes';
                break;
            case 'Ciudad Guayana':
                $response = 'Ciudad en constantes cambios y evolución';
                break;
            case 'Coro':
                $response = 'Cultura vibrante y colorida en cada rincón';
                break;
            case 'Valencia':
                $response = 'Ciudad procera de ensueños';
                break;
            case 'Vargas':
                $response = 'Para los amantes de la costas y la aventura';
                break;
            case 'Isla de Margarita':
                $city = 'Margarita';
                $response = 'La Perla del Caribe espera por ti';
                break;
            case 'Maracaibo':
                $response = 'Recorre los majestuosos suelos marabinos';
                break;
            case 'Colonia Tovar':
                $response = 'Un pedacito de Alemania en Venezuela';
                break;
            case 'Choroní':
                $response = 'Viaja a esta hermosa franja costera';
                break;
            case 'Ocumare de la Costa':
                $response = 'Disfruta de este paraíso Aragüeño';
                break;
            case 'Araure':
                $response = 'Auténtica ciudad colonial';
                break;
            case 'Maracay':
                $response = 'La ciudad jardín de Venezuela';
                break;
            case 'Mérida':
                $response = 'Paisajes hermosos, un clima perfecto';
                break;
            case 'Táriba':
                $response = 'Pequeña ciudad con una gran devoción';
                break;
            case 'Trujillo':
                $response = 'Territorio pintoresco con encanto andino';
                break;
            case 'Tucacas':
                $response = 'Ciudad playera de aguas cristalinas';
                break;
            case 'Falcón':
                $response = 'Paraíso de playas y desierto';
                break;
            case 'Los Roques':
                $response = 'Paraíso de arenas blancas';
                break;
            case 'Ciudad Ojeda':
                $response = 'Importante metrópolis Oriental';
                break;
            case 'San Cristóbal':
                $response = 'Encantadora ciudad andina';
                break;
            case 'El Tigre':
                $response = 'En todo el corazón de Anzoátegui';
                break;
            default:
                $response = null;
        }

        return $response;
    }


    /**
     * La siguiente función se encarga de obtener las rutas de las imagenes
     * de las ciudades de los establecimiento
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $city
     * @return string
     * @version 27/11/2015
     */
    private function getPathImageCity($city)
    {
        $file = $_SERVER['DOCUMENT_ROOT'] . '/images/home/temporal/' . Slug::generateSlug($city) . '.jpg';

        if (!file_exists($file))
            $path = 'images/home/temporal/venezuela.jpg';
        else
            $path = 'images/home/temporal/' . Slug::generateSlug($city) . '.jpg';

        return $path;
    }

    /**
     * La siguiente función chequea las localidades consideradas especiales
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $location
     * @return bool
     * @version 26/01/2016
     */
    private function checkSpecialLocation($location)
    {
        $response = false;

        switch ($location)
        {
            case 'tucacas':
                $response = 'Tucacas';
                break;
            case 'los-roques':
                $response = 'Los Roques';
                break;
            case 'choroni':
                $response = 'Choroní';
                break;
            case 'colonia-tovar':
                $response = 'Colonia Tovar';
                break;
            case 'ocumare-de-la-costa':
                $response = 'Ocumare de la Costa';
                break;
        }

        return $response;
    }

    /**
     * La siguiente función chequea las localidades consideradas especiales
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $location
     * @return bool
     * @version 26/01/2016
     */
    private function getCoordenate($slug)
    {
        switch ($slug)
        {
            case "aragua":
                return '{"latitude": 10.177150, "longitude": -67.242845, "perimeter": "10.496712, -67.847606,10.358225, -67.797667,10.339311, -67.636992,10.259474, -67.637231,10.204830, -67.563740,10.141336, -67.566190,10.122848, -67.640497,10.043257, -67.657645,10.026371, -67.529445,9.978649, -67.517889,9.984059, -67.366827,9.632222, -66.866949,9.488675, -67.018011,9.236848, -66.723791,9.484046, -66.576189,9.985555, -66.624742,10.119416, -67.071431,10.268511, -67.069489,10.469104, -67.217091, 10.444123, -67.303851,10.545994, -67.371761"}';
                break;
            case "araure":
                return '{"latitude": 9.570673, "longitude": -69.2301148, "perimeter": "9.626735, -69.251597,9.584759, -69.279063,9.502300, -69.276505,9.511442, -69.208527,9.563922, -69.207841,9.571031, -69.176598,9.597775, -69.174538,9.595406, -69.202004,9.628917, -69.206811"}';
                break;
            case "barcelona":
                return '{"latitude": 9.2171402, "longitude": -65.2246656, "perimeter":"10.183742, -64.737236,10.127475, -64.717027,10.083705, -64.692777,10.045046, -64.697396,10.041067, -64.661020,10.074041, -64.658710,10.074610, -64.605012,10.145664, -64.620602,10.159537, -64.647883,10.180086, -64.649340,10.176263, -64.691581,10.184865, -64.698621"}';
                break;
            case "barinas":
                return '{"latitude": 8.623264, "longitude": -70.237446, "perimeter":"8.679537, -70.271598,8.667997, -70.276233,8.652385, -70.255462,8.639826, -70.263874,8.653403, -70.273143,8.623024, -70.321037,8.589078, -70.308506,8.602827, -70.272113,8.584156, -70.254947,8.585344, -70.240356,8.569898, -70.225765,8.568370, -70.205509,8.568370, -70.205509,8.585174, -70.185939,8.566842, -70.162593,8.569728, -70.153152,8.586532, -70.167743,8.594849, -70.159847"}';
                break;
            case "barquisimeto":
                return '{"latitude": 10.067348, "longitude": -69.347988, "perimeter":"10.121349, -69.347058,10.032110, -69.469281,9.992891, -69.461384,10.020953, -69.357701,10.056112, -69.301739,10.050703, -69.278050,10.062197, -69.255391,10.113575, -69.271184,10.112223, -69.298993,10.129460, -69.309636"}';
                break;
            case "bolívar":
                return '{"latitude": 7.384900, "longitude": -64.929533, "perimeter":"6.152122, -67.654142,3.514067, -62.962980,4.927424, -60.480070,8.016357, -60.073576,8.538205, -63.050871,7.994598, -65.709562,7.243240, -67.159757"}';
                break;
            case "caracas":
                return '{"latitude": 10.469309, "longitude": -66.892478, "perimeter":"10.542312, -66.984439,10.427869, -67.070270,10.400855, -67.019115,10.426180, -66.901012,10.397816, -66.891399,10.395790, -66.812778,10.436647, -66.730380,10.480538, -66.712184"}';
                break;
            case "choroni":
                return '{"latitude": 10.498201, "longitude": -67.610594, "perimeter":"10.505037, -67.619177,10.486301, -67.616859,10.476848, -67.612911,10.481575, -67.605701,10.506049, -67.600208,10.507653, -67.592140,10.514573, -67.602955"}';
                break;
            case "ciudad-guayana":
                return '{"latitude": 8.285115, "longitude": -62.736292, "perimeter": "8.285097, -62.898006,8.160732, -62.820415,8.283058, -62.521037,8.418931, -62.621287,8.340131, -62.810115"}';
                break;
            case "ciudad-ojeda":
                return '{"latitude": 10.214807, "longitude": -71.313293, "perimeter": "10.243356, -71.374748,10.170034, -71.335609,10.186086, -71.251667,10.249944, -71.289432,10.246228, -71.369426"}';
                break;
            case "colonia-tovar":
                return '{"latitude": 8.327048, "longitude": -71.756258, "perimeter": "10.406337, -67.306581,10.399204, -67.301645,10.395827, -67.288084,10.409334, -67.264824,10.419084, -67.265339,10.419759, -67.268558,10.420224, -67.292547,10.412500, -67.303448"}';
                break;
            case "coro":
                return '{"latitude": 11.4023206, "longitude": -69.676431, "perimeter": "11.417896, -69.700979,11.393160, -69.706472,11.391814, -69.724839,11.380875, -69.726213,11.380707, -69.709047,11.370610, -69.706815,11.379529, -69.653257,11.439265, -69.619096"}';
                break;
            case "el-tigre":
                return '{"latitude": 8.8793034, "longitude": -64.2569148, "perimeter":"8.903144, -64.301797,8.832831, -64.263833,8.825984, -64.201330,8.841674, -64.181265, 8.861198, -64.167479, 8.873070, -64.119929,8.917844, -64.169024,8.909990, -64.185596,8.919259, -64.228901,8.927958, -64.226880,8.923109, -64.268741"}';
                break;
            case "falcon":
                return '{"latitude": 11.1995588, "longitude": -70.3470056, "perimeter":"11.036374, -71.313688,10.237375, -70.810197,10.723380, -69.710832,10.600816, -68.218836,11.213137, -68.246551,12.253335, -69.997221,12.000433, -70.426805"}';
                break;
            case "gran-sabana":
                return '{"latitude": 5.2646538, "longitude": -62.8083406, "perimeter":"6.419158, -62.827469,4.034272, -62.234207,4.932385, -60.487381,6.129765, -61.481644,6.495574, -62.745072"}';
                break;
            case "guatire":
                return '{"latitude": 10.4718353, "longitude": -66.581634, "perimeter":"10.475023, -66.587927,10.444975, -66.577627,10.433833, -66.527159,10.437040, -66.511709,10.454091, -66.505186,10.478905, -66.509649,10.497135, -66.524755,10.510300, -66.570589,10.489202, -66.577627"}';
                break;
            case "los-roques":
                return '{"latitude": 11.8573983, "longitude": -67.31801, "perimeter":"11.985737, -66.991328,11.702029, -66.959482,11.750000, -66.548750,12.008900, -66.627141"}';
                break;
            case "maracaibo":
                return '{"latitude": 10.6296598, "longitude": -71.74838, "perimeter":"10.766457, -71.773785,10.504616, -71.775845,10.498540, -71.569165,10.767132, -71.583584"}';
                break;
            case "maracay":
                return '{"latitude": 10.2534098, "longitude": -67.6390592, "perimeter":"10.277649, -67.667898,10.234744, -67.672705,10.202307, -67.577948,10.238460, -67.538465,10.329330, -67.538809,10.333721, -67.562841,10.267852, -67.605413"}';
                break;
            case "isla-de-margarita":
                return '{"latitude": 10.9962249, "longitude": -64.1644116, "perimeter": "11.192654, -63.777219,11.146190, -64.450067, 10.700408, -64.430470,10.735710, -63.700462"}';
                break;
            case "miranda":
                return '{"latitude": 10.2876291, "longitude": -66.6256519, "perimeter": "10.675055, -66.300040,10.554742, -66.364709,10.566094, -66.861271,10.452552, -66.886676,10.448009, -67.179994,10.111677, -67.087611,9.970676, -66.549476,9.991147, -65.588686,10.127593, -65.366966,10.609229, -65.992865"}';
                break;
            case "merida":
                return '{"latitude": 8.5874676, "longitude": -71.1886606, "perimeter": "8.563848, -71.222772,8.551130, -71.219914,8.558599, -71.161325,8.626219, -71.122538,8.635705, -71.148873,8.630457, -71.182352,8.591502, -71.217056"}';
                break;
            case "ocumare-de-la-costa":
                return '{"latitude": 10.4500655, "longitude": -67.7879362, "perimeter":"10.469961, -67.773435,10.450168, -67.771204,10.450379, -67.764423,10.468400, -67.761419,10.486652, -67.764109,10.486865, -67.777389"}';
                break;
            case "punto-fijo":
                return '{"latitude": 11.7039168, "longitude": -70.204339, "perimeter":"11.724213, -70.227771,11.669077, -70.220904,11.674457, -70.173354,11.695638, -70.142970,11.748080, -70.173697"}';
                break;
            case "san-cristobal":
                return '{"latitude": 7.7693255, "longitude": -72.2574695, "perimeter":"7.800539, -72.269655,7.731654, -72.271887,7.696782, -72.260042,7.748664, -72.191549,7.818567, -72.167517,7.804791, -72.263132"}';
                break;
            case "trujillo":
                return '{"latitude": 9.4782687, "longitude": -70.8450285, "perimeter":"9.637390, -71.093581,9.201154, -71.206191,8.989614, -70.547011,9.060141, -70.121291,9.772754, -69.942763,10.064951, -70.629409,9.634683, -71.088088"}';
                break;
            case "tucacas":
                return '{"latitude": 10.7796165, "longitude": -68.3635648, "perimeter":"10.829443, -68.350347,10.790999, -68.344339,10.729444, -68.320134,10.733998, -68.312581,10.794371, -68.308118,10.830623, -68.340562"}';
                break;
            case "tachira":
                return '{"latitude": 7.9982648, "longitude": -72.4786707, "perimeter":"8.575817, -72.514363,7.395381, -72.481404,7.447129, -71.316853,8.007784, -71.528340,8.192688, -71.871663,8.605690, -71.726094,8.703441, -72.006245"}';
                break;
            case "tariba":
                return '{"latitude": 7.8170615, "longitude": -72.2480252, "perimeter":"7.813788, -72.266135,7.788872, -72.237039,7.809876, -72.228284,7.807665, -72.198587,7.833770, -72.196698,7.844144, -72.209916"}';
                break;
            case "valencia":
                return '{"latitude": 10.225703, "longitude": -68.009421, "perimeter": "10.275873, -67.985369,10.272664, -68.030859, 10.092097, -68.104421,10.134004, -67.917007"}';
                break;
            case "vargas":
                return '{"latitude": 10.5244849, "longitude": -67.1354424, "perimeter":"10.584562, -67.412844,10.403620, -67.330446,10.475199, -67.017336,10.550812, -66.981631,10.523810, -66.336184,10.645303, -66.321078"}';
                break;
            default:
                return null;
                break;
        }
    }

}
