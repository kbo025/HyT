<?php
/**
 * Created by PhpStorm.
 * User: isabel nieto <isabelcnd@gmail.com>
 * Date: 06/06/16
 * Time: 12:28 PM
 */

namespace Navicu\InfrastructureBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Application\Contract\EmailInterface;
use Navicu\Core\Application\UseCases\Extranet\NotifyTheUnavailabilityInProperties\NotifyTheUnavailabilityInPropertiesCommand;


class EmailNotificationOfPropertyWithoutPriceCommand extends ContainerAwareCommand
{
    /**
     *   instancia del repositoryFactory
     *   @var RepositoryFactory $rf
     */
    protected $rf;
    /**
     * La variable contiene la funcionalidades del servicio de email
     *
     * @var EmailInterface $emailService
     */
    protected $emailService;

    /**
     * Método Get del la interfaz del serivicio Email
     * @internal param EmailInterface $emailService
     * @return \Navicu\Core\Application\Contract\EmailInterface
     */
    public function getEmailService()
    {
        return $this->emailService;
    }

    /**
     * Método Set del la interfaz del serivicio Email
     * @param EmailInterface $emailService
     */
    public function setEmailService(EmailInterface $emailService)
    {
        $this->emailService = $emailService;
    }
    
    protected function configure()
    {
        $this
            ->setName('navicu:propertiesEmail:without-price')
            ->setDescription('Revisión de los properties sin disponibilidad los próximos 30 días')
        ;
    }

    /**
     * Funcion encargada de revisar todos los properties y enviar un correo al admin (yzuzolo)
     * avisando los dias en los cuales los properties no tienen cargadas fechas con disponibilidades
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 30/06/2016
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $kernel;
        $container = $kernel->getContainer();
        $response = [];

        $rf = $container->get('RepositoryFactory');

        $properties = $rf->get('Property')->findAllProperty();
        foreach ($properties as $property) {
            if ($property->getActive()) {
                $data['slug'] = $property->getSlug();
                $command = new NotifyTheUnavailabilityInPropertiesCommand($data);
                $propertiesData = $container->get('CommandBus')->execute($command);

                $propertyResponse = $propertiesData->getData();
                $lengthRoom = count($propertyResponse['dailyRoom']);
                $lengthPack = count($propertyResponse['dailyPack']);

                if (($lengthRoom > 0) || ($lengthPack > 0)) {
                    $arrayResponse['propertyName'] = $propertyResponse['propertyName'];
                    if ($lengthRoom > 0)
                        $dateRoom = $propertyResponse['dailyRoom'][0];
                    else
                        $dateRoom = $propertyResponse['dailyPack'][0];

                    $commercialProfile = $property->getCommercialProfile();
                    if ($commercialProfile) {
                        $arrayResponse['responsible'] = $commercialProfile->getFullName();
                    } else
                        $arrayResponse['responsible'] = null;

                    $arrayResponse['date'] = $dateRoom['date'];
                    array_push($response, $arrayResponse);
                }
            }
        }

        $this->sendEmailAlert($response, $container);
    }

    /**
     * Funcion encargada de enviar los correos con la informacion recolectada de los properties
     * con dias que no tienen tarifas cargadas
     *
     * @param $response
     * @param $container
     *
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 30/06/2016
     */
    public function sendEmailAlert($response, $container)
    {
        $dataEmail["dataEmail"] = $response;

        $emailService = $container->get('EmailService');

        $emailService->setConfigEmail('first_mailer');

        $emailService->setRecipients(["yzuzolo@navicu.com"]);
        $emailService->setViewParameters($dataEmail);

        $emailService->setViewRender('NavicuInfrastructureBundle:Email:propertyWithoutPrice.html.twig');
        $emailService->setSubject('Revisar tarifas próximas a vencer - navicu.com');

        $emailService->setEmailSender('info@navicu.com');

        $emailService->sendEmail();
    }
}