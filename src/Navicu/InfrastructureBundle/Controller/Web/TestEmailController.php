<?php

namespace Navicu\InfrastructureBundle\Controller\Web;

use Navicu\Core\Domain\Model\Entity\AAVV;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


/**
 * El siguiente controlador se encarga de ser pruebas de los correos
 * del sistema, es el acceso a las vista de los correos
 *
 * Class TestEmailController
 * @package Navicu\InfrastructureBundle\Controller\Web
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 08/01/2016
 */
class TestEmailController extends Controller
{
    /**
     * La siguiente función carga la plantilla de los correos del sistema
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $email
     * @param $send
     * @return Response
     */
    public function loadTemplateAction($email, $send)
    {
        $template = null;
        $data = [];
        switch ($email) {
            case 1:
                $template = 'reservationConfirmation.html.twig';
                $aavv = new AAVV();
                $data['isCustomize'] = $aavv->getCustomize();
                $data['logo'] = '12312';
                break;
            case 2:
                $template = 'preReservationConfirmation.html.twig';
                break;
            case 3:
                $template = 'signInClient.html.twig';
                break;
            case 4:
                $template = 'extranetUpdate.html.twig';
                break;
            case 5:
                $template = 'reservationCanceled.html.twig';
                break;
            case 6:
                $template = 'propertyWithoutPrice.html.twig';
                break;
            case 7:
                $template = 'confirmAAVVRegistration.html.twig';
                break;
            case 8:
                $template = 'aavvReservation.html.twig';
                break;
            case 9:
                $template = 'registrationAAVV.html.twig';
                break;
            case 10:
                $template = 'insufficientCreditAAVV.html.twig';
                break;
        }

        if ($template) {
            $template = 'NavicuInfrastructureBundle:Email\Template:'.$template;

            if ($send == 1)
                $this->sendEmail($template, ['fcontreras@navicu.com'],[]);

            return $this->render($template,$data);
        }

        return new Response('No se cargo ninguna maquetación');
    }

    public function loadCampaignAction($email, $send)
    {
        $template = null;
        $data = [];
        switch ($email) {
            case 1:
                $template = 'campaign1.html.twig';
                break;
        }

        if ($template) {

            $template = 'NavicuInfrastructureBundle:Email\Newsletter:'.$template;

            if ($send == 1)
                $this->sendEmail($template, ['fcontreras@navicu.com'],[]);

            return $this->render($template,$data);
        }

        return new Response('No se cargo ninguna maquetación');
    }

    /**
     * Envio del correo
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $view
     * @param $emails
     */
    private function sendEmail($view, $emails, $data)
    {
        $emailService = $this->get('EmailService');
        $emailService->setConfigEmail('first_mailer');
        $emailService->setRecipients($emails);
        $emailService->setViewParameters($data);
        $emailService->setViewRender($view);
        $emailService->setSubject('Confirmación de la Reserva - Navicu');
        $emailService->setEmailSender('info@navicu.com');
        $emailService->sendEmail();
    }
}
