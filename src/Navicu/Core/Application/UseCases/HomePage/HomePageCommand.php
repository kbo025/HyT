<?php

namespace Navicu\Core\Application\UseCases\Prueba;

use Navicu\Core\Application\Contract\Command;


class HomePageCommand implements Command
{
	private $repository;
	
	public function __construct($repository = null)
	{
		$this->repository = $repository;
	}

	public function getRepository()
	{
		return null;
	}
}