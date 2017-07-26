<?php
namespace Navicu\Core\Application\UseCases\AAVV\Register\Step4\GetAAVVAgreement;

use Navicu\Core\Application\Contract\CommandBase;
use Navicu\Core\Application\Contract\Command;

class GetAAVVAgreementCommand extends CommandBase implements Command
{
    /**
     * identificador de la agencia de viaje a la que se le hará
     *
     * @var string $slug
     */
    protected $slug;

    /**
     * indica si el usuario aavv intentó finalizar su registro
     *
     * @var bool $finish
     */
    protected $finish;

}