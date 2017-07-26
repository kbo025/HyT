<?php
namespace Navicu\Core\Application\UseCases\Search\SearchCountry;

use Navicu\Core\Application\Contract\Command;


/**
 * La siguiente clase se encarga de retornas los estados por un país
 *
 * Class SearchCountryCommand
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @package Navicu\Core\Application\UseCases\Search\SearchCountry
 * @version 19/01/2016
 */
class SearchCountryCommand implements Command
{
    /**
     * @var Representa el codigo del pais a buscar
     */
    private $countryCode;

    public function __construct($countryCode = null)
    {
        $this->countryCode = $countryCode;
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
        return ['countryCode' => $this->countryCode];
    }

    /**
     * @return mixed
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param mixed $countryCode
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
    }
}