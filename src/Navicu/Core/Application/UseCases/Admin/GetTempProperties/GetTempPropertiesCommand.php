<?php
namespace Navicu\Core\Application\UseCases\Admin\GetTempProperties;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\CommandBase;

/**
 * Class GetTempPropertiesCommand
 *
 * Caso de uso de consulta de información basica de los establecimientos
 * que se encuentran en proceso de registro (TempOwner)
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 26/01/2017
 */
class GetTempPropertiesCommand implements Command
{
    /**
     * @var User Representa el usuario que consulta el caso de uso
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