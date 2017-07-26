<?php
namespace Navicu\Core\Application\UseCases\Search\GetDestinyOfLocation;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Model\ValueObject\Slug;

/**
 * Metodo que hace uso del motor de busqueda para generar una lista de
 * destinos para cliente.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class GetDestinyOfLocationHandler implements Handler
{
    /**
     *  Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle( Command $command, RepositoryFactoryInterface $rf )
    {
        $sphinxQL = $rf->get('SERepository');
        $location = $rf->get('Location');

        $destinies = $sphinxQL->destinationsList();

        $auxDestiny = array();
        for ($d = 0; $d < count($destinies); $d++) {

            $destiny = $location->find($destinies[$d]);
            $name = $destiny->getTitle();

            $auxLocation['slug'] = $destiny->getSlug();
            $auxLocation['name'] = $name;
            $auxLocation['description'] = $this->getDescription($name);
            $auxLocation['countryCode'] = $destiny->getRoot()->getAlfa2();
            $auxLocation['type'] = $destiny->checkType();
            $auxLocation['path'] = $this->getPathImageCity($name);

            $order[$d] = $auxLocation['name'];
            array_push($auxDestiny, $auxLocation);
        }
        array_multisort($order, SORT_ASC, $auxDestiny);

        return new ResponseCommandBus(200, 'Ok', $auxDestiny);

    }

    /**
     * Esta función retorna la descripcion de una ciudad.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param Request $city
     * @return string
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
     * Esta función se encarga de obtener las rutas de las imagenes
     * de las ciudades de los establecimiento.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param Request $city
     * @return string
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
}
