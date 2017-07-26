<?php
namespace Navicu\InfrastructureBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Navicu\Core\Application\UseCases\Admin\TransferReservationExpiration\TransferReservationExpirationCommand;

/**
 * El siguiente handler es usado para ejecutar el caso de uso TransferReservationExpiration
 * por medio de un comando en la terminal.
 * 
 * Class AdminCommand
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class ReservationExpirationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('navicu:reservation:expiration')
            ->setDescription('Verificación de las pre-reservas que no fueron procesadas en 24 horas')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $kernel;
		$container = $kernel->getContainer();
        $command = new TransferReservationExpirationCommand();
        $response = $container->get('CommandBus')->execute($command);

        if ($response->getStatusCode() == 200 )
            $output->writeln(json_encode($response->getData()));
        $output->writeln(json_encode($response->getData()));
        $output->writeln($response->getStatusCode());
    }
}
