<?php

namespace Navicu\Core\Application\UseCases\PruebaError;

use Navicu\Core\Application\Contract\Command;


class PruebaErrorCommand implements Command
{
	private $text;

	public function __construct($text)
	{
		$this->text = $text;
	}

	public function getRequest()
	{
		return array('text'=>$this->text);
	}

}