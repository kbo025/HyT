<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 08/09/16
 * Time: 08:39 AM
 */

namespace Navicu\Core\Application\UseCases\AAVV\Register\Step3\GetBillingAddress;

use Navicu\Core\Application\Contract\Command;

class GetBillingAddressCommand implements Command
{
    private $slug;

    public function __construct($data)
    {
        $this->slug = $data['slug'];
    }

    /**
     *  metodo getRequest devuelve un array con los parametros del command
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 05-05-2015
     * @return  Array
     */
    public function getRequest()
    {
        return [
            'aavv' => $this->slug,
        ];
    }
}