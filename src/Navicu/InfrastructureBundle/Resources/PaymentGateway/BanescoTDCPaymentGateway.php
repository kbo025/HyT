<?php
/**
 * Created by PhpStorm.
 * User: developer2
 * Date: 07/10/15
 * Time: 10:44 AM
 */

namespace Navicu\InfrastructureBundle\Resources\PaymentGateway;

use Navicu\Core\Application\Contract\PaymentGateway;
use Navicu\Core\Domain\Adapter\EntityValidationException;
use Navicu\Core\Domain\Model\Entity\Reservation;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;

class BanescoTDCPaymentGateway implements PaymentGateway
{

    /**
     *  constante que define una antelación general para los PaymentGateway
     */
    const CUTOFF = 0;

    /**
     * indica el estado de la transacción tras un evento de pago
     */
    private $valid_card = [
        414754, 439475, 439476, 522836, 546774, 546803, 627166, 603122, 603256, 541431, 541434, 602695, 406260,
        411047, 421884, 421885, 421886, 422714, 448174, 454134, 454135, 454136, 454188, 455612, 455613, 455614, 455615, 455627,
        455630, 455631, 455724, 455726, 455727, 455729, 459347, 462229, 480433, 493802, 493803, 493804, 512399, 517648, 517657,
        518031, 518152, 518225, 521877, 525739, 540019, 540060, 540075, 540084, 540131, 540142, 540143, 540144, 540145, 541395,
        541407, 541417, 541459, 541463, 542037, 542209, 542624, 546690, 546694, 547290, 547291, 547552, 547588, 549115, 549192,
        552267, 552292, 552590, 554770, 554790, 558491, 558753, 558834, 558841, 589941, 601705, 520173, 522131, 546474, 601759,
        406267, 407440, 411850, 411851, 414764, 422050, 422228, 430906, 476515, 486520, 494170, 499929, 499930, 517707, 518269,
        518310, 524621, 526749, 536570, 604841, 621984, 411032, 411096, 421930, 422099, 452517, 452518, 453230, 453231, 453232,
        453233, 455640, 455641, 456714, 467126, 479320, 479321, 498870, 498871, 501877, 501878, 517758, 527561, 530470, 530471,
        530472, 541247, 541844, 544680, 546088, 547303, 547304, 547793, 548851, 548852, 549190, 550291, 552266, 558870, 588891,
        402765, 411097, 420198, 421177, 421178, 437774, 438095, 439460, 439461, 439462, 446870, 450604, 450630, 450914, 454041,
        454042, 454439, 454440, 473210, 518002, 531295, 539608, 540009, 540628, 542007, 546890, 547326, 547327, 547589, 549197,
        552283, 556605, 589524, 601686, 411019, 422225, 451315, 451325, 454137, 454138, 454139, 457567, 476820, 481290, 489995,
        533416, 540085, 540086, 540132, 549193, 552284, 552299, 603644, 404849, 411018, 414786, 415229, 421882, 433121, 440827,
        454732, 456032, 456033, 456034, 456035, 479344, 518093, 525973, 541154, 543725, 547032, 547469, 549196, 552325, 627534,
        377000, 377010, 377020, 377030, 377031, 377032, 377033, 377034, 377035, 377036, 377037, 377038, 377039, 402686, 407616,
        409709, 414640, 422242, 441132, 441133, 446844, 450961, 450962, 454619, 467478, 474154, 493752, 493753, 517859, 539658,
        540133, 540137, 540932, 543212, 544388, 544395, 552265, 554388, 554395, 554665, 554912, 554937, 558844, 601400, 603071,
        473209, 601653, 500784, 543694, 548505, 554771, 554791, 415270, 415271, 491951, 554566, 554772, 554792, 491508, 491509,
        601418, 439813, 439814, 455608, 455609, 455610, 455611, 456804, 490735, 407457, 422268, 427170, 434613, 434614, 434615,
        439482, 439483, 518067, 522824, 545174, 554920, 554972, 558762, 603684, 455708, 455709, 455710, 455711, 473276, 473277,
        545194, 400145, 405761, 409940, 409941, 409942, 415762, 455334, 602693, 013400, 037024, 121099, 123097, 123099, 370243,
        370244, 370245, 371590, 379948, 405932, 411016, 411098, 414530, 421463, 422123, 422169, 450966, 451384, 451385, 451721,
        451722, 451785, 453502, 454500, 454501, 454502, 454504, 454511, 454512, 454513, 454514, 454515, 454516, 454517, 454518,
        454519, 454520, 454530, 454531, 454532, 454533, 454534, 454560, 454561, 454573, 454574, 454575, 454576, 454577, 454578,
        454579, 454580, 454587, 454588, 454589, 454599, 456010, 456011, 456013, 456349, 490788, 490789, 491952, 492457, 496638,
        496676, 496677, 497476, 515827, 523751, 540139, 540140, 540141, 540167, 540470, 540471, 540966, 542094, 542172, 542218,
        542660, 542695, 544194, 544312, 544338, 545049, 546465, 546466, 546492, 546704, 546752, 546753, 547289, 547887, 549189,
        552130, 552311, 554642, 554729, 601288, 601685, 603693, 700013, 824400, 824401, 824402, 824403, 824404, 824490, 824501,
        824601, 999999, 434957, 434958, 434959, 199407, 422039, 453299, 457999, 490112, 490113, 491270, 522963, 548685, 548849,
        548850, 552287, 601618, 434967, 441784, 441785, 441786, 552286, 554798, 554831, 554914, 558886, 621999, 411037, 411038,
        414755, 422230, 514871, 541838, 541839, 550523, 552326, 602904, 517677, 522847, 524369, 553480, 554210, 554211, 603208,
        434963, 434964, 452490, 452491, 458183, 458184, 524083, 552264, 628001, 627101, 504157, 531241, 531707, 547153, 553635,
        603555, 400606, 405890, 410808, 410809, 411858, 450685, 454000, 467477, 475783, 475784, 476560, 476561, 476562, 518150,
        518854, 522270, 539667, 541614, 541721, 541748, 541782, 547630, 554646, 554735, 554748, 554934, 589950, 602954, 603216,
        405898, 414768, 421037, 422654, 471240, 499889, 522835, 522974, 524892, 525712, 534523, 639589, 422271, 425888, 433485,
        433486, 521359, 541841, 541842, 552462, 601582, 411848, 421039, 512177, 541487, 541494, 550568, 554540, 554541, 554547,
        554548, 602000, 515671, 515856, 522299, 603954, 421895, 458129, 458130, 515591, 550478, 550479, 552285, 514008, 530634,
        545220, 554726, 627998, 457380, 457381, 457415, 457416, 474632, 492008, 492009, 492012, 492014, 516447, 517668, 519707,
        521778, 521872, 536440, 542467, 550280, 601750, 407556, 422260, 422261, 422262, 422263, 512742, 512776, 522782, 524339,
        539660, 553438, 639489, 510808, 514026, 518215, 521304, 528839, 545192, 639781, 522523, 524443, 529890, 606401, 427172,
        474496, 474497, 474498, 474499, 517953, 521041, 530147, 546629, 547554, 606061, 512391, 516668, 523739, 585948, 441058,
        441059, 476812, 476813, 476814, 512460, 515659, 515835, 522206, 527356, 541689, 543863, 548586, 555347, 628155, 900805,
        414747, 421444, 434965, 434966, 441040, 441041, 444806, 473292, 491608, 491609, 499941, 517856, 517857, 521305, 522946,
        522975, 534339, 544807, 544909, 545193, 552293, 553663, 554611, 554633, 515817, 527029, 528521, 528940, 553513, 448741,
        448742, 451449, 451450, 508111, 519723, 546489, 546490, 548686, 552565, 554929, 422044, 422045, 422046, 425881, 446334,
        515673, 515892, 520048, 520148, 520154, 528004, 552327, 627609, 544518, 554518, 602692, 524344, 554799, 554833, 603124,
        550567, 603061, 421034, 421035, 421036, 517981, 518539, 627398, 554500, 554534, 425478, 425479, 504934, 517674
    ];

    /**
     * indica el estado de la transacción tras un evento de pago
     */
    private $success;

    /**
     * variables de configuración necesarias para establecer la comunicacionm con la entidad bancaria
     *
     * @var Array;
     */
    private $config;

    /**
     * posibles estados de la transaccion
     *
     * @var String;
     */
    private $states;

    /**
     * constructor de la clase
     *
     * @var Array;
     */
    public function __construct(array $config)
    {
        /*
         * Tipos de estados de la transacción
         *
         *  1: 'Los datos de la tarjeta son invalidos'
         *  2: 'La fecha de la vencimiento de la tarjeta es invalida'
         *  3: 'La cedula ingresada no coincide con la tarjera'
         *  4: 'El codigo CVC es invalido'
         *  5: 'La tarjeta no posee fondos suficientes para la transaccion'
         *  6: 'La tarjeta no esta activada para transacciones electronicas'
         */
        $this->config = $config;
        $this->states = [
            '00' => 'APROBADO',
            '01' => 6,
            '02' => 3,
            '03' => 1,
            '04' => 1,
            '05' => 6,
            '06' => 1,
            '07' => 1,
            '12' => 3,
            '13' => 3,
            '14' => 1,
            '15' => 1,
            '19' => 7,
            '21' => 6,
            '25' => 1,
            '28' => 1,
            '30' => 3,
            '39' => 3,
            '40' => 1,
            '41' => 1,
            '43' => 1,
            '51' => 5,
            '52' => 3,
            '53' => 3,
            '54' => 2,//'Su tarjeta se encuentra vencida',
            '55' => 3,//'La clave indicada no es valida, por favor verifique e intente de nuevo',
            '57' => 6,
            '58' => 6,
            '61' => 1,
            '62' => 1,
            '63' => 1,
            '65' => 1,
            '68' => 7,
            '71' => 1,
            '72' => 1,
            '75' => 1,
            '76' => 3,
            '77' => 3,
            '78' => 3,
            '79' => 2,//'La fecha ingresada es invalida, por favor verifique e intente nuevamente',
            '80' => 7,
            '81' => 3,//'La clave indicada no es valida, por favor verifique e intente de nuevo',
            '82' => 1,
            '83' => 4,
            '84' => 1,
            '85' => 1,
            '86' => 3,//'La clave indicada no es valida, por favor verifique e intente de nuevo',
            '87' => 1,
            '88' => 1,
            '89' => 1,
            '90' => 7,
            '91' => 7,
            '92' => 7,
            '93' => 7,
            '94' => 1,
            '95' => 1,
            '96' => 7,
            '97' => 7,
            '99' => 1,//'Hubo un error de comunicación con el banco, por favor intente mas tarde',
        ];
    }

    /**
     * este metodo debe establecer la comunicacion con el banco y solicitar procesar el pago
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 07-10-2015
     * @param $request
     * @param $url
     * @param $method
     * @throws EntityValidationException
     * @return  Object | \Navicu\Core\Application\Contract\json | array
     */
    public function processPayment($request,$url,$method)
    {
        if ($this->validateRequestData($request)) {
            $request['PublicKeyId'] = $this->config['public_id'];
            $request['KeyId'] = $this->config['private_id'];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$this->config[$url] );
            if($method==='POST')
                curl_setopt($ch, CURLOPT_POST, 1);
            else
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($request));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            $response = $this->formaterResponseData(['request'=>$request,'response'=>$response]);
            $this->success = $this->success && $response['success'];
            return $response;
        } else {
            return false;
        }
    }

    public function processPayments($request)
    {
        $response = [];
        $status = true;
        if (empty($request))
            throw new EntityValidationException('payments', \get_class($this), 'empty_payments');
        else {
            $this->success = true;
            foreach ($request as &$payment) {
                $currentResponse = $this->processPayment($this->formaterRequestData($payment),'url_payment_petition','POST');
                $status = $status && $currentResponse['success'];
                $response[] = $currentResponse;
            }
            /*foreach ($request as $current) {
                if ($current['response']['success']) {
                    $new = [
                        'id' => $current['response']['id'],
                        'amount' => $current['amount']
                    ];
                    $response[] = $this->processPayment(
                        $new,
                        'url_complete_payment_petition',
                        $status ? 'POST' : 'DELETE'
                    );
                }
            }*/
        }
        return $response;
    }

    /**
     * debe validar que todos los campos requeridos para hacer la solicitud esten completos y sean correctos
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 07-10-2015
     * @param $request
     * @throws EntityValidationException
     * @return  boolean
     */
    public function validateRequestData($request)
    {
        if(empty($request['CardNumber']) || !$this->checkLuhn($request['CardNumber']) || !$this->checkVenezuelanCard($request['CardNumber']))
            throw new EntityValidationException('payments',\get_class($this),'invalid_card_number');
        if(empty($request['ExpirationDate']) || !$this->checkExpiredDate($request['ExpirationDate']))
            throw new EntityValidationException('payments',\get_class($this),'invalid_expiration_date');
        if(empty($request['Amount']) || !is_numeric($request['Amount']) )
            throw new EntityValidationException('payments',\get_class($this),'invalid_amount');
        if(empty($request['Description']))
            throw new EntityValidationException('payments',\get_class($this),'invalid_description');
        if(empty($request['CardHolder']))
            throw new EntityValidationException('payments',\get_class($this),'invalid_card_holder');
        if(empty($request['CardHolderId']))
            throw new EntityValidationException('payments',\get_class($this),'invalid_card_holder_id');
        if(empty($request['CVC']) || !is_numeric($request['CVC']) || strlen($request['CVC'])!=3)
            throw new EntityValidationException('payments',\get_class($this),'invalid_cvc');
        if(empty($request['StatusId']))
            throw new EntityValidationException('payments',\get_class($this),'empty_status_id');
        if(empty($request['IP']))
            throw new EntityValidationException('payments',\get_class($this),'empty_ip');

        return true;
    }

    /**
     * debe devolver la data requerida para hacer la solicitud formateada segun los requerimientos de la entidad
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 07-10-2015
     */
    public function formaterRequestData($request)
    {
        $amount = str_replace(",","",(string)$request['amount']);  //Eliminar las comas del monto a cobrar

        return [
            'Amount' => (string)$amount,
            'Description' => $request['description'],
            'CardHolder' => $request['holder'],
            'CardHolderId' => (string)$request['holderId'],
            'CardNumber' => $request['number'],
            'CVC' => $request['cvc'],
            'StatusId' => "2",
            'ExpirationDate' => $request['ExpirationDate'],
            'IP' => $request['ip']
        ];
    }

    /**
     * debe devolver la data requerida para ser devuelta a el caso de uso segun el formato que necesite
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 07-10-2015
     */
    public function formaterResponseData($response)
    {
        $request = $response['request'];
        $jsonResponse = $response['response'];
        $response = json_decode($jsonResponse,true);
        $amount = str_replace(",","",(string)$request['Amount']);  //Eliminar las comas del monto a cobrar
        return array_merge($response,[
            'id' => $response['id'],
            'success' => $response['success'],
            'code' => $response['code'],
            'reference' => $response['reference'],
            'holder' => $request['CardHolder'],
            'holderId' => $request['CardHolderId'],
            'status' => $response["success"] ? 1 : 2,
            'amount' => $amount,
            'response' => $jsonResponse,
        ]);
    }

    /**
     * devuelve un array clave valor con el formato 'Codigo de estado' => 'estado de la transaccion'
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 07-10-2015
     *
     * @return Array
     */
    public function getStates()
    {
        return $this->states;
    }

    public function getTypePayment()
    {
        return PaymentGateway::BANESCO_TDC;
    }

    private function checkLuhn($input)
    {
        $sum = 0;
        $numdigits = strlen($input);
        $parity = $numdigits % 2;
        for($i=0; $i < $numdigits; $i++) {
            $digit = (int)substr($input, $i, 1);

            if($i % 2 == $parity)
                $digit *= 2;
            if($digit > 9)
                $digit -= 9;

            $sum += $digit;
        }

        return ($sum % 10) == 0;
    }

    private function checkExpiredDate($expired)
    {
        $now = new \DateTime();
        $date = explode('/', $expired);

        if ((int)$date[0] < 12) {
            $date[0] = ((int)$date[0]) + 1;
        } else {
            $date[0] = 1;
            $date[1] = ((int)$date[1]) + 1;
        }

        $date = new \DateTime($date[1] .'-'. $date[0] .'-01');

        return $date > $now;
    }

    /**
     * devueleve un entero que representa el estado de la reserva segun la condicion de los pagos
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 07-10-2015
     */
    public function getStatusReservation(Reservation $reservation)
    {
        $status = null; //default pre-reserva pendiente por pago
        $paid = false;
        $total = 0;
        foreach ($reservation->getPayments() as $payment) {
            if ($payment->getStatus() == 1)
                $total = $total + $payment->getAmount();
            $paid =  ($total >= $reservation->getTotalToPay());
        }
        if($paid)
            $status = 2; //confirmada
        return $status;
    }

    /**
    * indica si el pago es valido y correcto
    */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * chequea que el numero de la tarjeta corresponda a una tarjeta emitida en Venezuela
     * @param $card
     * @return bool
     */
    private function checkVenezuelanCard($card)
    {
        //Chequea si se encuentra en producción se enviara los correos
        global $kernel;

        if ($kernel->getEnvironment() == 'prod') {
            $digits = substr($card, 0, 6);
            return in_array((int)$digits, $this->valid_card);
        }
        return true;
    }

    public function getCutOff()
    {
        return self::CUTOFF;
    }

    /**
     * establece en que moneda se realizaran los cobros
     */
    public function setCurrency($currency = 'VEF', RepositoryFactoryInterface $rf = null)
    {
        if ($currency!='VEF')
            throw new EntityValidationException('invalid_currency_for_paymentgateway',1);
    }

    public function getCurrency()
    {
        return 'VEF';
    }
}