<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 19/08/16
 * Time: 02:33 PM
 */

namespace Navicu\Core\Application\UseCases\Web\ForeignExchange\GetListCurrency;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

class GetListCurrencyHandler implements Handler
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
        $currencyTypeRf = $rf->get('CurrencyType');

        $currency = $currencyTypeRf->getAllCurrencyActive();
        $response = [];
        foreach ($currency as $coin) {
            $objCurrency['alpha'] = $coin->getAlfa3();
            $objCurrency['title'] = $coin->getTitle();
            $objCurrency['sym'] = (!empty($coin->getSimbol())) ? $coin->getSimbol() : "";
            array_push($response, $objCurrency);
        }
        return new ResponseCommandBus(200, 'Ok', $response);
    }
}