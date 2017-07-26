<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 02/09/16
 * Time: 08:52 AM
 */

namespace Navicu\InfrastructureBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Navicu\Core\Application\UseCases\Admin\CurrencyExchange\CurrencyExchangeCalendar\GetCurrencyExchangeCalendar\GetCurrencyExchangeCalendarCommand;
use Navicu\Core\Application\UseCases\Admin\CurrencyExchange\CurrencyExchangeCalendar\UpdateCurrencyExchangeCalendar\UpdateCurrencyExchangeCalendarCommand;
use Navicu\Core\Application\UseCases\Admin\CurrencyExchange\CurrencyExchangeGraphic\GetCurrencyExchangeGraphicCommand;
use Navicu\Core\Application\UseCases\Admin\CurrencyExchange\GetCurrencyExchangeHistoricLocale\GetCurrencyExchangeHistoricLocaleCommand;
use Navicu\Core\Application\UseCases\Admin\CurrencyExchange\GetCurrencyExchangeDollarHistoric\GetCurrencyExchangeDollarHistoricCommand;

class CurrencyExchangeController extends Controller
{
    /**
     * Funcion encargada de reenderizar la vista con los valores del calendario
     *
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 21/07/2016
     * @return JsonResponse
     */
    public function getViewCurrencyExchangeCalendarAction() {
        return $this->render('NavicuInfrastructureBundle:Admin:currencyExchangeCalendar/index.html.twig');
    }

    /**
     * Funcion encargada de enviar los datos a la vista del calendario
     *
     * @param Request $request
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 21/07/2016
     * @return JsonResponse
     */
    public function getCurrencyExchangeCalendarAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);
            $command = new GetCurrencyExchangeCalendarCommand($data['data']);
            $response = $this->get('CommandBus')->execute($command);
            if ( ($response->getStatusCode() >= 200) && ($response->getStatusCode() < 300) ) {
                return new JsonResponse($response->getData(), $response->getStatusCode());
            }
            return new JsonResponse($response->getData(), 400);
        }
        else
            return new JsonResponse("Bad Request", 400);
    }

    /**
     * Funcion encargada de ingresar los nuevos valores del calendario y devolver la
     * informacion de la base de datos actualizada
     *
     * @param Request $request
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 25/07/2016
     * @return JsonResponse
     */
    public function updateCurrencyExchangeCalendarAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);

            $command = new UpdateCurrencyExchangeCalendarCommand($data['data']);
            $response = $this->get('CommandBus')->execute($command);

            if ( ($response->getStatusCode() >= 200) && ($response->getStatusCode() < 300) ) {
                $command = new GetCurrencyExchangeCalendarCommand();
                $response = $this->get('CommandBus')->execute($command);
                if (($response->getStatusCode() >= 200) && ($response->getStatusCode() < 300))
                    return new JsonResponse($response->getData(), $response->getStatusCode());
                else
                    return new JsonResponse($response->getData(), $response->getStatusCode());
            }
            return new JsonResponse($response->getData(), $response->getStatusCode());
        }
        return new JsonResponse("Bad Request", 400);
    }

    /**
     * Funcion encargada de devolver toda la informacion referente a los
     * cambios oficiales y paralelos en bolivares
     *
     * @author Isabel Nieto <isabelnieto@gmail.com>
     * @version 27/07/2016
     * @param Request $request, array con la fecha inicio y fecha fin de la busqueda
     * @return JsonResponse
     */
    public function getCurrencyExchangeGraphicAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);

            $command = new GetCurrencyExchangeGraphicCommand($data['data']);
            $response = $this->get('CommandBus')->execute($command);
            if ( ($response->getStatusCode() >= 200) && ($response->getStatusCode() < 300) ) {
                return new JsonResponse($response->getData(), $response->getStatusCode());
            }
            return new JsonResponse($response->getData(), $response->getStatusCode());
        }
        return new JsonResponse("Bad Request", 400);
    }

    /**
     * Funcion encargada de devolver toda la informacion referente a los
     * cambios oficiales y paralelos en bolivares
     *
     * @author Isabel Nieto <isabelnieto@gmail.com>
     * @version 28/07/2016
     * @param Request $request, array con la fecha inicio y fecha fin de la busqueda
     * @return JsonResponse
     */
    public function getCurrencyExchangeHistoricLocaleAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);

            $command = new GetCurrencyExchangeHistoricLocaleCommand($data['data']);
            $response = $this->get('CommandBus')->execute($command);
            if ( ($response->getStatusCode() >= 200) && ($response->getStatusCode() < 300) )
                return new JsonResponse($response->getData(), $response->getStatusCode());
            else
                return new JsonResponse($response->getData(), $response->getStatusCode());
        }
        return new JsonResponse("Bad Request", 400);
    }

    /**
     * Funcion encargada de devolver toda la informacion referente a los
     * cambios del dolar a las demas monedas almacenadas
     *
     * @author Isabel Nieto <isabelnieto@gmail.com>
     * @version 28/07/2016
     * @param Request $request, array con la fecha inicio y fecha fin de la busqueda
     * @return JsonResponse
     */
    public function getCurrencyExchangeDollarHistoricAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);

            $command = new GetCurrencyExchangeDollarHistoricCommand($data['data']);
            $response = $this->get('CommandBus')->execute($command);
            if ( ($response->getStatusCode() >= 200) && ($response->getStatusCode() < 300) )
                return new JsonResponse($response->getData(), $response->getStatusCode());
            return new JsonResponse($response->getData(), $response->getStatusCode());
        }
        return new JsonResponse("Bad Request", 400);
    }
}
