<?php
namespace Navicu\Core\Application\Contract;

/**
 * Interface Handler modela las funciones que obligatoriamente deben
 * implementarse en un objeto Handler
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 * @version 05-05-2015
 */
interface Handler
{
    /**
     *  Ejecuta las tareas solicitadas 
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf);


}