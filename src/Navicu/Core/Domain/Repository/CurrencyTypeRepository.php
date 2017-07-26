<?php
namespace Navicu\Core\Domain\Repository;

/**
*	@author Gabriel Camacho <kbo025@gmail.com>
*	@author Currently Working: Gabriel Camacho
*/
interface CurrencyTypeRepository
{
	public function getAllCurrency();
	public function findOneBy(array $criteria);
	public function find($id);
	public function getAllCurrencyActive();
	public function findAll();
}