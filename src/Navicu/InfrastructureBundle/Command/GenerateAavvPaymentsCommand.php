<?php

namespace Navicu\InfrastructureBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Navicu\Core\Application\UseCases\AAVV\Billing\GeneratePayments\GeneratePaymentsCommand;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class GenerateAavvPaymentsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('navicu:aavv:generatepayments')
            ->setDescription('Generacion de pagos asociados a las facturas por reservas y servicios')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $kernel;
        $container = $kernel->getContainer();
        $command = new GeneratePaymentsCommand();
        $response = $container->get('CommandBus')->execute($command);

        if ($response->getStatusCode() == 200 ){
            $data = $response->getData();
            $output->writeln('Generados '. $data['Reservation'] . ' pagos por Reservas ');
            $output->writeln('Generados '. $data['Service'] . ' pagos por Servicios ');
        }

    }
}