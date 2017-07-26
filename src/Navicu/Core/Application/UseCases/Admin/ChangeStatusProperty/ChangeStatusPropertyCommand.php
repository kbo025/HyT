<?php
namespace Navicu\Core\Application\UseCases\Admin\ChangeStatusProperty;

use Navicu\Core\Application\Contract\Command;


class ChangeStatusPropertyCommand implements Command
{
    /**
    *   el id publico del establecimiento al cual se le cambiara el estado
    *   @var string $id
    */
    protected $id;

    /**
    *   el estado por el cual queremos cambiar (activo: true, inactivo: false)
    *   @var boolean $status
    */
    protected $status;

	public function __construct($id,$status)
	{
        $this->id = $id;
        $this->status = $status;
	}

	public function getRequest()
	{
        return array(
            'id' => $this->id,
            'status' => $this->status
        );
	}

    public function get($att)
    {
        return ( isset($this->$att) ? $this->$att : null );
    }
}