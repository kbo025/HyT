<?php
namespace Navicu\Core\Domain\Repository;

/**
*	@author Gabriel Camacho <kbo025@gmail.com>
*	@author Currently Working: Juan Pablo Osorio V.
*/
interface AccommodationRepository
{
	public function getAccommodationList();

	public function getAllWithKeys();

    public function getById($id);

    public function getByName($name);
}

/* End of file */