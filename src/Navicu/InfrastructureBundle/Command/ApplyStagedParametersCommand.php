<?php
/**
 * Created by PhpStorm.
 * User: developer10
 * Date: 25/01/17
 * Time: 08:47 AM
 */

namespace Navicu\InfrastructureBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Navicu\Core\Application\UseCases\Admin\AAVVModule\ApplyParameters\ApplyParametersCommand;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class ApplyStagedParametersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('navicu:aavv:applyparameters')
            ->setDescription('Comando para aplicar los parametros agendados para el dia actual')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $kernel;
        $container = $kernel->getContainer();
        $command = new ApplyParametersCommand();
        $response = $container->get('CommandBus')->execute($command);

        if ($response->getStatusCode() == 201 ){
            $output->writeln('Comando Ejecutado Exitosamente');
        }

    }
}