<?php
namespace Navicu\InfrastructureBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * El siguiente commando se encarga de verificar los correos electronicos que no
 * fueron enviados de manera correcta,
 *
 * Class  CheckFailuresEmails
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 */
class CheckFailuresEmailsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('navicu:emails:check-failures')
            ->setDescription('Verifica cuales fueron los correos que no fueron enviados')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $kernel;
        $container = $kernel->getContainer();
        $emailService = $container->get('EmailService');

        $output->writeln($emailService->checkFailures());
    }
}
