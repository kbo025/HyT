<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 15/12/16
 * Time: 09:12 AM
 */

namespace Navicu\InfrastructureBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ActivateAavvUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('navicu:aavv:user-activation')
            ->setDescription('Activacion de nuevos usuario luego de ser agregados')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $users = [];
        global $kernel;
        $container = $kernel->getContainer();
        $rf = $container->get('DbRepositoryFactory');

        $repoAavvProfile = $rf->get('AAVVProfile');
        $repoUser = $rf->get('User');

        // Actualizamos los usuarios de la tabla de aavv_profile
        $aavvUserActivated = $repoAavvProfile->changeStatusOfAavvProfile();


        $length = count($aavvUserActivated['idAavvProfile']);
        for ($ii = 0; $ii < $length; $ii++)
            array_push($users, $aavvUserActivated['idAavvProfile'][$ii][1]);

        // Se actualizan de la tabla fos_user los perfiles de las aavv
        $fosUserActivated = $repoUser->updateStatus($users);

        $output->writeln('users activated '. $fosUserActivated);
    }
}
