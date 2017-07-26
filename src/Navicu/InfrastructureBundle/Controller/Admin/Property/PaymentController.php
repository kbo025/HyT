<?php
namespace Navicu\InfrastructureBundle\Controller\Admin\Property;

use Navicu\Core\Application\UseCases\Admin\UpdatePaymentProperty\UpdatePaymentPropertyCommand;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;



class PaymentController extends Controller
{

    public function paymentAction(Request $request,$slug)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);
            $command = new UpdatePaymentPropertyCommand($slug,$data['payment']);
            $response = $this->get('CommandBus')->execute($command);
            return new JsonResponse($response->getData(),$response->getStatusCode());
        } else {
            $service = $this->get('AdminProperties');
            $data = $service->getPaymentData($slug);
            return $this->render(
                'NavicuInfrastructureBundle:Ascribere:affiliateAdmin/editPayment.html.twig',
                array('data' => json_encode($data), "slugTemp" => $slug)
            );
        }
    }
}
