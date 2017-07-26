<?php
namespace Navicu\InfrastructureBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CleanReserveLockExpiredCommand extends ContainerAwareCommand
{

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('navicu:reservation:cleanexpired')
            ->setDescription('Greet someone');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $kernel;
        $container = $kernel->getContainer();
        $service = $container->get('LockedAvailabilityService');
        $rf = $container->get('DbRepositoryFactory');
        $service::cleanExpiredLockAvailability($rf);
        $output->writeln(date('d.m.Y H:i:s'));
        return 1;
    }
}