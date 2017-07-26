<?php

namespace Navicu\Core\Application\UseCases\Reservation\getInfoByRedSocial;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

/**
 * El siguiente handler devuelve la información de un usuario
 * Cliente por medio de su ID de RedSocial.
 * 
 * Class getInfoByRedSocial
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class getInfoByRedSocialHandler implements Handler
{

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
    public function handle(Command $command, RepositoryFactoryInterface $rf = null)
    {
        $request = $command->getRequest();

		$redSocial = $rf->get("RedSocial")->findOneByArray(
            array(
                "id_social"=>$request["idRedSocial"],
                "type"=>$request["redSocial"]
            ));

		if (!$redSocial)
			return new ResponseCommandBus(400, 'Ok', array("register"=>false));

		$client = $redSocial->getClient();
		if ($client->getEmail() == $request["email"]){
            $response = $client->toArray();
            $response["register"]= true;
			return new ResponseCommandBus(200, 'Ok', $response);
        }
		else{
			return new ResponseCommandBus(400, 'Ok', array("register"=>false));
        }
    }
}