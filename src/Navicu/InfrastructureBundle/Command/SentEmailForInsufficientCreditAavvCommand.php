<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 25/11/16
 * Time: 10:44 AM
 */

namespace Navicu\InfrastructureBundle\Command;


use Navicu\Core\Application\Contract\EmailInterface;
use Navicu\Core\Domain\Model\Entity\AAVV;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command encargado de enviar los correos diaramente notificando a las agencias afiliadas que se
 * han quedado sin credito disponible
 *
 * Class SentEmailForInsufficientCreditAavvCommand
 * @package Navicu\InfrastructureBundle\Command
 */
class SentEmailForInsufficientCreditAavvCommand extends ContainerAwareCommand
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
            ->setName('navicu:aavv:InsufficientCreditEmail')
            ->setDescription('Envio diario de correos por credito insuficiente de las aavv')
        ;
    }

    /**
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $kernel;
        $container = $kernel->getContainer();
        $sent = 0;
        $email = [];

        $rf = $container->get('DbRepositoryFactory');

        $aavvs = $rf->get('AAVV')->findAll();

        foreach ($aavvs as $aavv) {
            if ( ($aavv->getDeactivateReason() == 3) AND ($aavv->getCreditAvailable() < 0) ) {
                $profiles = $aavv->getAavvProfile();
                foreach ($profiles as $profile) {
                    $email[] = $profile->getEmail();
                }
                if (!empty($email)) {
                    $this->sendEmailAlert($email, $container, $aavv);
                    $sent += count($email);
                    $email = [];
                }
            }
        }
        $output->writeln($sent.'Correos enviados exitosamente ');
    }

    /**
     * Funcion encargada de enviar los correos a las aavv que no tengan credito disponible
     *
     * @param $emails
     * @param $container
     *
     * @param $aavv
     * @internal param $response
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 24/11/2016
     */
    public function sendEmailAlert($emails, $container, $aavv)
    {
        $emailService = $container->get('EmailService');

        $emailService->setConfigEmail('first_mailer');

        $emailService->setRecipients($emails);

        $emailService->setViewParameters(['deactivateAavv' => true, 'agencyName' => $aavv->getCommercialName()]);

        $emailService->setViewRender('NavicuInfrastructureBundle:Email:insufficientCreditAAVV.html.twig');
        $emailService->setSubject('Recargar crédito');

        $emailService->setEmailSender('info@navicu.com');

        $emailService->sendEmail();
    }
}