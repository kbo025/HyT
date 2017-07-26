<?php


namespace Navicu\InfrastructureBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Navicu\Core\Application\UseCases\AAVV\Billing\GenerateInvoices\GenerateInvoicesCommand;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
class GenerateAavvInvoicesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('navicu:aavv:generateinvoices')
            ->setDescription('Generacion de las facturas mensuales de las agencias de viaje')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $kernel;
        $container = $kernel->getContainer();
        $command = new GenerateInvoicesCommand();
        $response = $container->get('CommandBus')->execute($command);

        if ($response->getStatusCode() == 200 ){
            $data = $response->getData();
            $output->writeln('Generadas '. $data['generated'] . ' Facturas.');
        } elseif ($response->getStatusCode() == 404) {
            $data = $response->getData();
            $output->writeln($data['message']);
        }

    }
}