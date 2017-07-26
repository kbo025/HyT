<?php
namespace Navicu\Core\Application\UseCases\AAVV\Register\Step4\ValidateRegistration;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

class ValidateRegistrationCommand extends CommandBase implements Command
{

    /**
     * Obteo AAVV o string que representa un slug de un objeto AAVV
     *
     * @var mixed $slug
     */
    protected $aavv;

    /**
     * indica el paso del registro que debe validarse
     *
     * @var mixed $slug
     */
    protected $step;

    /**
     * variable que indica si el usuario intentó finalizar el registro o no
     *
     * @var boolean $finish
     */
    protected $finish;
} 