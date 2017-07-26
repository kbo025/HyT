<?php
namespace Navicu\Core\Application\UseCases\Web\ForeignExchange\getUserLocation;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;

/**
 * Comando para devolver si la ip de un usuario es de Venezuela o no.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class getUserLocationHandler implements Handler
{
    /**
     * Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle( Command $command, RepositoryFactoryInterface $rf )
    {
        $rpIpCollection = $rf->get('IpCollection');
        $request = $command->getRequest();
        $ip = $request["ip"];

        // Detectar si es ipv4 o ipv6
        if (strpos($ip, ".")) { // ipv4
            $ipInteger = ip2long($ip); // Cambio de formato de IP => INTEGER
        } else { // ipv6
            return new ResponseCommandBus(200, 'Ok', null);
        }

        $ipCollection =  $rpIpCollection->findByIpRange($ipInteger);
        
        if (!$ipCollection)
            return new ResponseCommandBus(200, 'Ok', ["location"=>'USA' , "currency" =>"USD", "sym" =>"$"]);

        $data["location"] = $ipCollection->getLocation()->getAlfa3();

        $currency = $ipCollection->getLocation()->getOfficialCurrency()->getAlfa3();
        $data["currency"] = $currency;

        $sym = $ipCollection->getLocation()->getOfficialCurrency()->getSimbol();
        $data["sym"] = $sym ? $sym : $currency;

        return new ResponseCommandBus(200, 'Ok', $data);
    }
}