<?php
/**
 * Created by Isabel Nieto <isabelcnd@gmail.com>.
 * User: user03
 * Date: 20/07/16
 * Time: 01:23 PM
 */

namespace Navicu\InfrastructureBundle\Command;

use Navicu\Core\Application\UseCases\Admin\CurrencyExchange\CurrencyExchangeCalendar\CreateCurrencyExchangeCalendar\CreateCurrencyExchangeCalendarCommand;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class CreateInfoFromWebApiCommand extends ContainerAwareCommand
{
    /**
     *   instancia del repositoryFactory
     *   @var RepositoryFactory $rf
     */
    protected $rf;

    protected function configure()
    {
        $this
            ->setName('navicu:DataFromWebApi:charge-price')
            ->setDescription('Carga de la informacion suministrada por la Api de dolar today y otras Apis')
        ;
    }

    /**
     * Funcion encargada de leer de las web api incluida dolar today, el cambio paralelo que se tiene
     *
     * @author: Isabel Nieto <isabelcnd@gmail.com>
     * @param InputInterface $input
     * @param OutputInterface $output
     */

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $kernel;
        $container = $kernel->getContainer();

        $command = new CreateCurrencyExchangeCalendarCommand();
        $container->get('CommandBus')->execute($command);
    }


}