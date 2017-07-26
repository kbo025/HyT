<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 17/08/16
 * Time: 01:49 PM
 */

namespace Navicu\Core\Application\UseCases\Web\ForeignExchange\SetAlphaCurrency;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreSession;

class SetAlphaCurrencyHandler implements Handler
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
        $request = $command->getRequest();

        // Validamos que la moneda que solicitan exista y este activa
        $currency = $this->validateAlphaCurrency($request["alphaCurrency"], $rf);
        if ($currency) {
            CoreSession::set("alphaCurrency", $request["alphaCurrency"]);
            CoreSession::set("userCurrency", $request["alphaCurrency"]);
            $location = CoreSession::get('userLocation');
            CoreSession::set("symbolCurrency", $location == "VEN" ? "Bs" : $request["alphaCurrency"]);
            return new ResponseCommandBus(200, 'Ok', true);
        }
        else
            return new ResponseCommandBus(400, 'Bad Request', false);
    }

    /**
     * Funcion encargda de validar que la moneda seleccionada exista y este activa en la base de datos
     *
     * @param string $alpha, tipo de moneda seleccionada
     * @param $rf
     * @version 18/08/2016
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @return mixed
     */
    public function validateAlphaCurrency($alpha, $rf)
    {
        $currencyRf = $rf->get('CurrencyType');
        $currency = $currencyRf->findOneBy(array('alfa3'=>$alpha, 'active'=>true));

        return $currency;
    }
}
