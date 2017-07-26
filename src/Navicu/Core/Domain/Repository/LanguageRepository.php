<?php

namespace Navicu\Core\Domain\Repository;

/**
*	@author Gabriel Camacho <kbo025@gmail.com>
*	@author Currently Working: Gabriel Camacho
*/
interface LanguageRepository
{
	public function getLanguagesList();
	public function findAllWithKeys();
}