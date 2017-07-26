<?php
namespace Navicu\Core\Application\Contract;

use Navicu\Core\Domain\Model\Entity\Reservation;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;

/**
 * Interface PaymentGateway modela las funciones que obligatoriamente deben implementarse un medio de pago implementado por y a traves de terceros
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 * @version 07-10-2015
 */
interface PaymentGateway
{

     /**
     * las constantes registran los posibles tipos de pagos que admite navicu
     */
    const BANESCO_TDC = 1;
    const NATIONAL_TRANSFER = 2;
    const STRIPE_TDC = 3;
    const INTERNATIONAL_TRANSFER = 4;
    const AAVV = 5;
    const PAYEEZY = 6;

    /**
     * este metodo toma un conjunto de pagos, los valida y los procesa
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 07-10-2015
     * @return  Object | json | array
     */
    public function processPayments($request);


    /**
     * debe validar que todos los campos requeridos para hacer la solicitud esten completos y sean correctos
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 07-10-2015
     * @return  boolean
     */
    public function validateRequestData($request);

    /**
     * debe devolver la data requerida para hacer la solicitud formateada segun los requerimientos de la entidad
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 07-10-2015
     */
    public function formaterRequestData($request);

    /**
     * debe devolver la data requerida para ser devuelta a el caso de uso segun el formato que necesite
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 07-10-2015
     */
    public function formaterResponseData($response);

    /**
     * devueleve un entero que representa el estado de la reserva segun la condicion de los pagos
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 07-10-2015
     */
    public function getStatusReservation(Reservation $reservation);

    /**
     * indica si el pago es valido y correcto
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 18-03-2015
    */
    public function isSuccess();

    /**
     *  funcion que devuelve el tipo de pago definido entre las constantes de esta interfaz
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 17-08-2016
     */
    public function getTypePayment();

    /**
     * devuleve la antelacion por defecto para el tipo de pago
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 17-08-2016
     */
    public function getCutOff();

    /**
     * devuelve un array clave => valor con los estados posibles de el tipo de reservas que implementa
     */
    public function getStates();

    /**
     * establece en que moneda se realizaran los cobros
     *
     * @param string $currency
     * @param RepositoryFactoryInterface $rf
     * @return Object
     */
    public function setCurrency($currency = 'VEF', RepositoryFactoryInterface $rf = null);

    /**
     * devuelve el tipo de moneda que maneja la instancia de paymentgateway
     */
    public function getCurrency();
}