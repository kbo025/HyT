<?php
namespace Navicu\Core\Application\UseCases\Ascribere\Rooms\SelectTempRoom;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;

class SelectTempRoomHandler implements Handler
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
        $request = $command->getRequest();
        $tempowner_repository = $rf->get('TempOwner');

        $tempowner = $tempowner_repository->findOneByArray(['slug'=>$request['slug']]);
        if (!empty($tempowner)) {

            $room = $tempowner->selectRoom( $request['index'] );
            if(isset($room))
                return new ResponseCommandBus(201,'OK',$room);

            return new ResponseCommandBus(400,'Bad Request',['message'=>'room not found']);
        }
        return new ResponseCommandBus(401,'Unauthorized');
    }
}