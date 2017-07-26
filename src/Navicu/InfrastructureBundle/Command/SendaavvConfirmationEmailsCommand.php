<?php
/**
 * Created by PhpStorm.
 * User: developer10
 * Date: 15/11/16
 * Time: 01:19 PM
 */

namespace Navicu\InfrastructureBundle\Command;

use Navicu\Core\Application\UseCases\AAVV\Reservation\SendConfirmationEmails\SendConfirmationEmailsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;


class SendaavvConfirmationEmailsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('navicu:aavv:sendconfirmationemails')
            ->setDescription('comando que realiza el envio programado de los correos de confirmacion de reservas')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $kernel;
        $container = $kernel->getContainer();
        $domain = $kernel->getContainer()->getParameter('domain');
        $data['domain'] = $domain;
        $command = new SendConfirmationEmailsCommand($data);
        $response = $container->get('CommandBus')->execute($command);

        if ($response->getStatusCode() == 200 ){
            $data = $response->getData();
            $output->writeln('Correos enviados exitosamente');
        }

    }
}