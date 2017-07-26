<?php
/**
 * Created by Isabel Nieto.
 * User: Isabel Nieto
 * Date: 11/05/16
 * Time: 12:26 PM
 */

namespace Navicu\Core\Application\UseCases\Extranet\NotifyTheUnavailabilityInProperties;

use Navicu\Core\Application\Contract\Command;

class NotifyTheUnavailabilityInPropertiesCommand implements Command
{
    /**
     * @var string $slug		Variable para el manejo de un slug.
     */
    private $slug;

    /**
     * @var User usuario a consultar las disponibilidades
     */
    private $user;

    /**
     * @var Fecha inicial a buscar
     */
    private $startDate;

    /**
     * @var Fecha final a buscar
     */
    private $endDate;
    private $search;
    private $orderBy;
    private $orderType;
    private $page;
    private $numberResult;

    public function __construct($data) {
        $this->slug = isset($data["slug"]) ? $data["slug"] : null;
        $this->user = isset($data["user"]) ? $data["user"] : null;
        $this->startDate = isset($data["startDate"]) ? new \DateTime($data['startDate']) : new \DateTime();
        $this->endDate = isset($data["endDate"]) ? new \DateTime($data['endDate']) : (new \DateTime())->modify('+30 days');
        $this->search = isset($data['search']) ? $data['search'] : null;
        $this->orderBy = isset($data['orderBy']) ? $data['orderBy'] : 'name';
        $this->orderType = isset($data['orderType']) ? $data['orderType'] : null;
        $this->page = isset($data['page']) ? $data['page'] : 1;
        $this->numberResult = 50;
    }

    /**
     * Devuelve un array con los datos que encapsula
     *
     * @return Array
     */
    public function getRequest()
    {
        return array(
            'slug' => $this->slug,
            'user' => $this->user,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'search' => $this->search,
            'order_by' => $this->orderBy,
            'order_type' => $this->orderType,
            'page' => $this->page,
            'number_result' => $this->numberResult
        );
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
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
     * @return Fecha
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param Fecha $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return Fecha
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param Fecha $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @param int $numberResult
     */
    public function setNumberResult($numberResult)
    {
        $this->numberResult = $numberResult;
    }

}