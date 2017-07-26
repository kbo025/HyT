<?php
namespace Navicu\Core\Application\UseCases\Admin\GetAffiliatedProperties;

use Navicu\Core\Application\Contract\Command;

/**
 * La clase es el comando del caso de uso "Obtener todos los establecimiento afiliados"
 * La clase no contiene atributos
 *
 * Class GetAffiliatedPropertiesCommand
 * @package Navicu\Core\Application\UseCases\Admin\GetAffiliatedProperties
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 16/10/2015
 */
class GetAffiliatedPropertiesCommand implements Command
{
    /**
     * @var User representa el usuario
     */
    protected $user;
    private $numberResult;
    private $search;
    private $orderBy;
    private $orderType;
    private $page;

    public function __construct($dataInput = null)
    {
        $this->user = null;
        $this->search = isset($dataInput['search']) ? $dataInput['search'] : null;
        $this->orderBy = isset($dataInput['orderBy']) ? $dataInput['orderBy'] : null;
        $this->orderType = isset($dataInput['orderType']) ? $dataInput['orderType'] : null;
        $this->page = isset($dataInput['page']) ? $dataInput['page'] : 1;
        $this->numberResult = 50;
    }

    public function getRequest()
    {
        return [
            'user' => $this->user,
            'search' => $this->search,
            'order_by' => $this->orderBy,
            'order_type' => $this->orderType,
            'page' => $this->page,
            'number_result' => $this->numberResult
        ];
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getNumberResult()
    {
        return $this->numberResult;
    }

    /**
     * @param mixed $numberResult
     */
    public function setNumberResult($numberResult)
    {
        $this->numberResult = $numberResult;
    }
}