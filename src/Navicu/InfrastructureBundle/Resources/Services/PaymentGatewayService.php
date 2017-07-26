<?php
namespace Navicu\InfrastructureBundle\Resources\Services;

use Navicu\InfrastructureBundle\Resources\PaymentGateway\AAVVPaymentGateway;
use Navicu\InfrastructureBundle\Resources\PaymentGateway\BanescoTDCPaymentGateway;
use Navicu\InfrastructureBundle\Resources\PaymentGateway\BanckTransferPaymentGateway;
use Navicu\InfrastructureBundle\Resources\PaymentGateway\PayeezyPaymentGateway;
use Navicu\InfrastructureBundle\Resources\PaymentGateway\StripeTDCPaymentGateway;
use Navicu\InfrastructureBundle\Resources\PaymentGateway\InternationalBanckTransferPaymentGateway;
use Navicu\Core\Application\Contract\PaymentGateway;

class PaymentGatewayService
{

    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getPaymentGateway($type)
    {
        if ($type==PaymentGateway::BANESCO_TDC) {
            //pago con TDC
            $config = $this->container->getParameter('payment_gateway');
            $paymenGateway = new BanescoTDCPaymentGateway($config['banesco_tdc']);
        } elseif ($type==PaymentGateway::NATIONAL_TRANSFER) {
            //Pago por transferencia bancaria nacional
            $paymenGateway = new BanckTransferPaymentGateway();
        } elseif ($type==PaymentGateway::STRIPE_TDC) {
            //Pago por TDC en moneda extranjera
            $config = $this->container->getParameter('payment_gateway');
            $paymenGateway = new StripeTDCPaymentGateway($config['stripe']);
        } elseif ($type==PaymentGateway::INTERNATIONAL_TRANSFER) {
            //Pago por Transferencia en moneda extranjera
            $paymenGateway = new InternationalBanckTransferPaymentGateway();
        } else if ($type == PaymentGateway::AAVV) {
            //Pago por agencia de viaje
            $paymenGateway = new AAVVPaymentGateway();
        } else if($type == PaymentGateway::PAYEEZY){
            $config = $this->container->getParameter('payment_gateway');
            $paymenGateway = new PayeezyPaymentGateway($config['payezeey']);
        } else {
            throw new \Exception('Tipo no definido');
        }
        return $paymenGateway;
    }
} 