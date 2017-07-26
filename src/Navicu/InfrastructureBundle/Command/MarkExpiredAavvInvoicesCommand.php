<?php

namespace Navicu\InfrastructureBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Navicu\Core\Application\UseCases\AAVV\Billing\MarkExpiredInvoices\MarkExpiredInvoicesCommand;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class MarkExpiredAavvInvoicesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('navicu:aavv:markexpiredinvoices')
            ->setDescription('comando que marca las facturas vencidas a la fecha actual')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $kernel;
        $container = $kernel->getContainer();
        $command = new MarkExpiredInvoicesCommand();
        $response = $container->get('CommandBus')->execute($command);

        if ($response->getStatusCode() == 200 ){
            $data = $response->getData();
            $output->writeln('Fueron marcadas '. $data['updated'] . ' Facturas como vencidas');
        }

    }
}