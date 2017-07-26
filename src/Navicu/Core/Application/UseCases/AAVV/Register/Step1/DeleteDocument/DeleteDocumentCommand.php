<?php


namespace Navicu\Core\Application\UseCases\AAVV\Register\Step1\DeleteDocument;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

class DeleteDocumentCommand extends CommandBase implements Command
{
    protected $path;
}