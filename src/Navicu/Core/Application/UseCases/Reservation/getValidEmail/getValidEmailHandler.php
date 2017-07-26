<?php

namespace Navicu\Core\Application\UseCases\Reservation\getValidEmail;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

/**
 * El siguiente handler busca si existe un correo electronico
 * en el registro de un usuario cliente registrado.
 * 
 * Class getValidEmail
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class getValidEmailHandler implements Handler
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

		$client = $rf->get("User")->findByUserNameOrEmail($request["email"]);

		if ($client)
			return new ResponseCommandBus(200, 'Ok', array("register"=>true));
		else
			return new ResponseCommandBus(400, 'Ok', array("register"=>false));
    }
}