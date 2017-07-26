<?php 

namespace Navicu\Core\Application\Services;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreValidator;
 

/**
 *
 * CommandBus se encarga de recibir los comandos que provienen de 
 * 
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 * @version 05/05/2015
 * 
 */
class CommandBus
{
    private $rf;

    private $email;

    private $managerBD;

    private $managerImage;

     /**
     * Create a new CommandBus
     *
     * @param Container $container
     * @return void
     */
    public function __construct($rf, $email = null, $managerBD = null, $managerImage)
    { 
        $this->rf = $rf;
        $this->email = $email;
        $this->managerBD = $managerBD;
        $this->managerImage = $managerImage;
    }
 
    /**
     * Execute a Command by passing it to a Handler
     *
     * @param Command $command
     * @return void
     */
    public function execute(Command $command)
    {
        $handler = $this->handler($command);
        if($handler!=null)
        {
            //Se valida automaticamente el commando antes de ser ejecutado
            $validation = CoreValidator::getValidator($command);
            if (is_null($validation)) {

                if (property_exists($handler, 'emailService'))
                    $handler->setEmailService($this->email);

                if (property_exists($handler, 'managerBD'))
                    $handler->setManagerBD($this->managerBD);

                if (property_exists($handler, 'managerImage'))
                    $handler->setManagerImage($this->managerImage);

                try {
                    return $handler->handle($command,$this->rf);
                } catch (\Exception $e) {
                    return new ResponseCommandBus(
                        500,
                        'Bad Request',
                        [
                            'code' => $e->getCode(),
                            'message' => $e->getMessage(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine()
                        ]
                    );
                }
                
            } else {
                return new ResponseCommandBus(400, 'Bad Request', $validation);
            }
        }else{
            return new ResponseCommandBus(404,'Handler not Found');
        }
    }
 
    /**
     * Get the Command Handler
     *
     * @return mixed
     */
    private function handler(Command $command)
    {
        $class = $this->inflect($command);
        if(\class_exists($class)){
            return new $class();
        }else{
            return null;
        }
    }

    /**
     * Encuentra un Manejador para un comando reemplazando 'Command' por 'Handler'
     *
     * @param Command $command
     * @return string
     */
    private function inflect(Command $command)
    {
        $commandclass = \get_class($command);
        $handlerclass =str_replace('Command', 'Handler', $commandclass);
        return $handlerclass;
    }
}