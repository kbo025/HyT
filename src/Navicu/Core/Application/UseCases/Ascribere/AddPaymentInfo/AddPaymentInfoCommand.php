<?php
namespace Navicu\Core\Application\UseCases\Ascribere\AddPaymentInfo;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

class AddPaymentInfoCommand extends CommandBase implements Command
{
    /**
    *
    */
    protected $slug;

    /**
    *
    */
    protected $same_data_property;

    /**
    *
    */
    protected $name;

    /**
    *
    */
    protected $tax_id;

    /**
    *
    */
    protected $location;

    /**
    *
    */
    protected $address;

    /**
    *
    */
    protected $currency_id;

    /**
    *
    */
    protected $swift;

    /**
    *
    */
    protected $charging_system;

    /**
    *
    */
    protected $account_number_part1;

    /**
    *
    */
    protected $account_number_part2;

    /**
    *
    */
    protected $account_number_part3;

    /**
    *
    */
    protected $account_number_part4;

    /**
    *
    */
    protected $is_admin;
}