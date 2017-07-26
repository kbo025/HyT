<?php

namespace Navicu\Core\Domain\Repository;

/**
* 	ContactPersonInterface es la interfaz que obliga a la infraestructura a implementar los metodos que manipulan los datos
*	de los contactos del establecimiento
*	@author Gabriel Camacho <kbo025@gmail.com>
*	@author Currently Working: Gabriel Camacho (17-07-15)
*/
interface ContactPersonRepository
{
    public function getListType();

    public function getIdType($string);

    public function getNameType($id);

    public function getRequiredType($id);
}