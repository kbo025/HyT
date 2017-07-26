<?php

namespace Navicu\InfrastructureBundle\Resources\Services;

use Navicu\Core\Application\Contract\EmailInterface;
/**
 *	La siguiente clase es el servicio donde se crea el uso de enviar email
 *
 *	@author Freddy Contreras <freddycontreras3@gmail.com>
 *	@author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 *	@version 12/08/2015
 */
class EmailService implements EmailInterface
{
    /**
     * @var La variable representa el container que permite hacer uso de los demás servicios
     */
    private $container;

    /**
     * @var string es un string que representa el email el cual se va a utilizar
     * La configuración de los email se realiza (config.yml,  sección swiftmailer)
     */
    private $configEmail;

    /**
     * @var string Representa la vista del email (html.twig)
     */
    private $viewRender;

    /**
     * @var array es el conjunto de parametros que se enviaran a la vista
     */
    private $viewParameters;

    /**
     * @var array son los destinarios del email
     */
    private $recipients;

    /**
     * @var string representa el asunto del email
     */
    private $subject;

    /**
     * @var string representa el email que se mostrar como remitente
     */
    private $emailSender;

    /**
     * La siguiente es el constructor de la clase
     *
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->configEmail = 'first_mailer';
        $this->viewRender = '';
        $this->viewParameters = array();
        $this->recipients = array();
        $this->subject = 'Info - Navicu';
        $this->emailSender = 'noreply@navicu.com';
    }

    /**
     * La siguiente función  envia el correo, recibiendo un conjunto de
     * parametros que dan especificaciones del correo
     *
     * @param array $parameters
     */
    public function sendEmail($parameters = array())
    {
        //Chequea si se encuentra en producción se enviara los correos
        global $kernel;

        if ($kernel->getEnvironment() == 'prod' or $kernel->getEnvironment() == 'stg') {
            if (!empty($parameters))
                $this->setParameters($parameters);

            $email = $this->container->get("swiftmailer.mailer." . $this->configEmail);
            $message = \Swift_Message::newInstance()
                ->setSubject($this->subject)
                ->setFrom(array($this->emailSender => 'navicu.com'))
                ->setCharset('UTF-8')
                ->setContentType('text/html')
                ->setBody(
                    $this->container->get('templating')->render(
                        $this->viewRender, $this->viewParameters
                    )
                );

            $domain = $kernel->getContainer()->getParameter('domain');

            if ($kernel->getEnvironment() == 'prod' && $domain == "navicu.com")
                $message->setTo($this->recipients);
            else if ($kernel->getEnvironment() == 'stg' && ($domain == "qa.nevicu.com" or $domain == "nevicu.com"))
                $message->setTo('noreply@navicu.com');
            else if ($kernel->getEnvironment() == 'stg' && $domain == "novicu.com" )
                $message->setTo('jacastronavicu@gmail.com');
            
            $email->send($message);
        }
    }

    /**
     * La función verifica si los ultimos correos enviados llegaron correctamente
     * a su destinatario
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 12/05/2016
     */
    public function checkFailures()
    {
        $client = new Client();
        $mg = new Mailgun("key-babcc663a53d4bab61353ba7da23b566", $client);
        $domain = "navicu.com";


        $result = $mg->get("$domain/bounces",[
            'event' => 'failed',
            'begin'        => date(DATE_RFC2822, strtotime('-10 min')),
            'ascending'    => 'yes'
        ]);

        $httpResponseCode = $result->http_response_code;
        $httpResponseBody = $result->http_response_body;

        $logItems = $result->http_response_body->items;
        return json_encode($logItems);
    }

    /**
     * La siguiente función realiza la asignación de los parametros
     * que dan caracteristica al correo
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $parameters
     * @version 12/08/2015
     */
    public function setParameters(array $parameters)
    {
        $this->configEmail = isset($parameters['configEmail']) ?
            $parameters['configEmail'] : $this->configEmail;

        $this->viewRender = isset($parameters['viewRender']) ?
            $parameters['viewRender'] : $this->viewRender;

        $this->viewParameters = isset($parameters['viewParameters']) ?
            $parameters['viewParameters'] : $this->viewParameters;

        $this->recipients = isset($parameters['recipients']) ?
            $parameters['recipients'] : $this->recipients;

        $this->subject = isset($parameters['subject']) ?
            $parameters['subject'] : $this->subject;

        $this->emailSender = isset($parameters['emailSender']) ?
            $parameters['emailSender'] : $this->emailSender;
    }

    /**
     * La siguiente función retorna el valor del atributo
     * @return string
     */
    public function getConfigEmail()
    {
        return $this->configEmail;
    }

    /**
     * La siguiente función asigna un valor al atributo $configEmail
     * @param string $configEmail
     */
    public function setConfigEmail($configEmail)
    {
        $this->configEmail = $configEmail;
    }

    /**
     * La siguiente función retorna el valor del atributo $viewRender
     * @return string
     */
    public function getViewRender()
    {
        return $this->viewRender;
    }

    /**
     * La siguiente función asigna un valor al atributo viewRender
     * @param string $viewRender
     */
    public function setViewRender($viewRender)
    {
        $this->viewRender = $viewRender;
    }

    /**
     * La siguiente función retorna el valor del atributo viewParameters
     * @return array
     */
    public function getViewParameters()
    {
        return $this->viewParameters;
    }

    /**
     * La siguiente función asigna un valor al atributo viewParameters
     * @param array $viewParameters
     */
    public function setViewParameters(array $viewParameters)
    {
        $this->viewParameters = $viewParameters;
    }

    /**
     * La siguiente función retorna el valor del atributo recipients
     * @return array
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * La siguiente función asigna un valor al atributo recipients
     * @param array $recipients
     */
    public function setRecipients(array $recipients)
    {
        $this->recipients = $recipients;
    }

    /**
     * La siguiente función retorna el valor del atributo $subject
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * La siguiente función asigna un valor al atributo $subject
     * @param string subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * La siguiente función retorna el valor del atributo $emailSender
     * @return string
     */
    public function getEmailSender()
    {
        return $this->emailSender;
    }

    /**
     * La siguiente función asigna un valor al atributo $emailSender
     * @param string
     */
    public function setEmailSender($emailSender)
    {
        $this->emailSender = $emailSender;
    }
}
