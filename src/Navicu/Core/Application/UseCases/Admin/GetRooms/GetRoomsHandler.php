<?php
namespace Navicu\Core\Application\UseCases\Admin\GetRooms;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;

/**
 * El siguiente handler busca las habitaciones de un establecimiento.
 * 
 * Class GetRoomsHandler
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class GetRoomsHandler implements Handler
{
    /**
     *   Instancia del repositoryFactory
     *   @var RepositoryFactory $rf
     */
    protected $rf;

    /**
     * Ejecuta las tareas solicitadas
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     * 
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * 
     * @return ResponseCommandBus
     */
    public function handle( Command $command, RepositoryFactoryInterface $rf )
    {
		$request = $command->getRequest();

        $slug = $request["slug"];
        $response = array();

        $repoProperty = $rf->get('Property');
        $property = $repoProperty->findOneBy(array('slug'=>$slug));

        if (!$property) {
			return new ResponseCommandBus(400, 'Ok', null);
        }

        $response['slug'] = $slug;
        $response['amount_rooms'] = $property->getAmountRoom();

        $objRooms = $property->getRooms();
        $cRooms = 0;
        $rooms = array();

        for ($r = 0; $r < count($objRooms); $r++) {
            if ($objRooms[$r]->getIsActive()) {
                $cRooms += $objRooms[$r]->getAmountRooms();

                $auxRoom["id"] = $objRooms[$r]->getId();
                $auxRoom["typeRoom"] = $objRooms[$r]->getName();
                $auxRoom["numRooms"] = $objRooms[$r]->getAmountRooms();
                $auxRoom["numPeople"] = $objRooms[$r]->getMaxPeople();
                $auxRoom["image"] = $objRooms[$r]->getProfileImage() ? $objRooms[$r]->getProfileImage()->getImage()->getFileName() : null;
                array_push($rooms, $auxRoom);
            }
        }

        $response['amount_rooms_added'] = $cRooms;
        $response["rooms"] = $rooms;


        return new ResponseCommandBus(200, 'Ok', $response);
    }
}