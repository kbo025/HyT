<?php
namespace Navicu\Core\Application\UseCases\Search\SearchCountryLocation;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Domain\Model\ValueObject\Slug;


/**
 * La siguiente clase se encarga de retornar suguerencias o listgado de establecimientos dado
 *
 *
 * Class SearchCountryLocationCommand
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @package Navicu\Core\Application\UseCases\Search\SearchCountry
 * @version 19/01/2016
 */
class SearchCountryLocationCommand implements Command
{
    /**
     * @var Representa el codigo del pais a buscar
     */
    private $countryCode;

    /**
     * @var Representa la localidad(ciudad/estado) a buscar en un país ($countryCode)
     */
    private $location;

    public function __construct($countryCode = null, $location = null)
    {
        $this->countryCode = $countryCode;
        $this->location = Slug::generateSlug($location,'_');
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
            'countryCode' => $this->countryCode,
            'location' => $this->location
        ];
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

    /**
     * @return null
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param null $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }
}