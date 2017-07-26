<?php

namespace Navicu\Core\Application\UseCases\AAVV\Preregister\GetValidAAVVEmail;

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
 * @author Alejandro Conde <adcs2008@gmail.com>
 * @author Currently Working: Alejandro Conde
 */
class GetValidAAVVEmailHandler implements Handler
{

    protected $rf;

    /**
     * Ejecuta las tareas solicitadas
     *
     * @author Alejandro Conde <adcs2008@gmail.com>
     * @author Currently Working: Alejandro Conde
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     *
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf = null)
    {
        $request = $command->getRequest();

		$client = $rf->get('User')->findByUserNameOrEmail($request["email"]);

        //var_dump($request["email"]);

		if ($client)
			return new ResponseCommandBus(200, 'Ok', array("register"=>true));
		else
			return new ResponseCommandBus(200, 'Ok', array("register"=>false));
    }
}
