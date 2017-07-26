<?php
namespace Navicu\Core\Application\UseCases\Web\getLocationRegister;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;

/**
 * Comando para devolver las localidades "Pais" y "Estados" para el registro
 * de un usuario cliente.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class getLocationRegisterHandler implements Handler
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
        $data = $command->getRequest();
        $repoLocation = $rf->get("Location");

        $countries = $repoLocation->findBy(["lvl"=>0]);
        $response["countries"] = $this->response($countries);

        $states = $repoLocation->findBy(["root"=>1,"lvl"=>1]);
        $response["states"] = $this->response($states);

        return new ResponseCommandBus(200, 'Ok', $response);
    }

    public function response($locations)
    {
        $res = [];
        foreach ($locations as $location){
            $aux["id"] = $location->getId();
            $aux["title"] = $location->getTitle();
            array_push($res,$aux);
        }
        return $res;
    }
}