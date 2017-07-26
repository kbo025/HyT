<?php

namespace Navicu\Core\Application\UseCases\GetClientProfile;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Domain\Model\Entity\ClientProfile;

/**
 * Created by Isabel Nieto.
 *
 * Conjunto de objetos para editar los valores ingresados del cliente perteneciente al caso de uso GetClientProfile
 *
 * User: user03
 * Date: 20/04/16
 * Time: 02:21 PM
 */
class GetClientProfileCommand implements Command
{
    /**
     * @var object $client_profile objeto obtenido de la session activa del tipo ClientProfile
     */
    private $client_profile;

    /**
     * GetClientProfileCommand constructor.
     * @param object $Client objeto del tipo ClientProfile
     */
    public function __construct($Client)
    {
        $this->client_profile = $Client;
    }

    /**
     *  metodo getRequest devuelve un array con los parametros del command
     *
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @return array objeto del tipo ClientProfile
     */
    public function getRequest()
    {
        return array(
            'clientProfile' => $this->client_profile,
        );
    }

    /**
     * Funcion que devuelve el objeto del tipo ClientProfile
     *
     * @return object ClientProfile del tipo ClientProfile
     */
    public function getClientProfile()
    {
        return $this->client_profile;
    }

    /**
     * Function que crea en la cache un nuevo cliente
     * @param ClientProfile $client_profile del tipo ClientProfile
     */
    public function setClientProfile($client_profile)
    {
        $this->client_profile = $client_profile;
    }

}