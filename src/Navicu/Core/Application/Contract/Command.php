<?php

namespace Navicu\Core\Application\Contract;

/**
* Interface Command modela las funciones que obligatoriamente deben implementarse en un Commando
*
* @author Gabriel Camacho <kbo025@gmail.com>
* @author Currently Working: Gabriel Camacho 
* @version 05-05-2015
*/
interface Command
{
	/**
	*  metodo getRequest devuelve un array con los parametros del command 
	*
	* @author Gabriel Camacho <kbo025@gmail.com>
	* @version 05-05-2015
	* @return  Array
	*/
    public function getRequest();
}