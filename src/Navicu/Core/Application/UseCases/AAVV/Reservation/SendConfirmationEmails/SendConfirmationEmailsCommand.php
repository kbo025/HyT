<?php
/**
 * Created by PhpStorm.
 * User: developer10
 * Date: 15/11/16
 * Time: 09:55 AM
 */

namespace Navicu\Core\Application\UseCases\AAVV\Reservation\SendConfirmationEmails;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

class SendConfirmationEmailsCommand extends CommandBase implements Command
{
    protected $domain;
}