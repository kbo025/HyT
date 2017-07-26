<?php
/**
 * Created by PhpStorm.
 * User: developer10
 * Date: 05/04/17
 * Time: 10:29 AM
 */

namespace Navicu\InfrastructureBundle\Command;

use Navicu\Core\Application\UseCases\FixImagesDirectories\FixImagesDirectoriesCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;


class FixGalleryDirectoriesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('navicu:fixgalleriesdirectories')
            ->setDescription('Comando para reparar inconsistencias entre los slug de los establecimientos y sus directorios de imagen')
            ->addArgument('route', InputArgument::REQUIRED, 'Ruta absulota a la carpeta web')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $basePath = $input->getArgument('route');

        $data['basePath'] = $basePath;//$basePath = '/home/developer10/Repos/navicu/web';
        global $kernel;
        $container = $kernel->getContainer();
        $command = new FixImagesDirectoriesCommand($data);
        $response = $container->get('CommandBus')->execute($command);

        if ($response->getStatusCode() == 200 ){
            $data = $response->getData();
            $output->writeln('Renombrados los directorios de  '. $data['fixed'] . ' Establecimientos.');
        } else {
            $output->writeln('Ha ocurrido un error');
        }

    }
}