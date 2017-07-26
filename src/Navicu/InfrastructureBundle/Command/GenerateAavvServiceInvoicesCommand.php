<?php


namespace Navicu\InfrastructureBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Navicu\Core\Application\UseCases\AAVV\Billing\GenerateServiceInvoices\GenerateServiceInvoicesCommand;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class GenerateAavvServiceInvoicesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('navicu:aavv:generateserviceinvoices')
            ->setDescription('Generacion de las facturas por servicios mensuales de las agencias de viaje')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $kernel;
        $container = $kernel->getContainer();
        $command = new GenerateServiceInvoicesCommand();
        $response = $container->get('CommandBus')->execute($command);

        if ($response->getStatusCode() == 200 ){
            $data = $response->getData();
            if($data['invoiceCount'] > 0)
                if($data['invoiceCount'] == 1)
                    $output->writeln($data['invoiceCount'].' Factura Generada Exitosamente');
                else
                    $output->writeln($data['invoiceCount'].' Facturas Generadas Exitosamente');
            else
                $output->writeln('No se Generó ninguna factura');
        }

    }
}