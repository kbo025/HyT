<?php

namespace Navicu\Core\Application\Contract;


/**
* 	ResponseCommandBus representa una repuesta del handler luego de solicitarle que ejecute un caso de uso
*
*	@author Gabriel Camacho <kbo025@gmail.com>
*	@author Currently Working: Gabriel Camacho (05-05-15)
*	@version 05/05/2015
*/
class ResponseCommandBus
{
	/**
	* @var integer $status_code  codigo que indica el estado de la accion ejecutada
	*/
	private $status_code;

	/**
	* @var string $message  estado de la accion ejecutada en estado natural
	*/
	private $message;

	/**
	* @var array $data array que contiene datos relevantes
	*/
	private $data;


    /**
     * Constructor de la clase
     *
     * @param integer $codigo codigo de estado de la accion
     * @param string $message mensaje que indica el estado de la accion
     * @param array $data array de datos relevantes para la accion ejecutado
     */
	public function __construct($codigo,$message,$data = null)
	{
		$this->status_code = $codigo;
		$this->message = $message;
		$this->data = $data;
	}

    /**
     * devuelve un array de datos relevantes para la accion ejecutada
     *
     * @return  array $data
     */
	public function getData()
	{
		return $this->data;
	}

    /**
     * devuelve un mensaje que indica el estado de la accion ejecutada
     *
     * @return  string $message
     */
	public function getMessage()
	{
		return $this->message;
	}

    /**
     * devuelve codigo de estado de la accion ejecutada
     *
     * @return  integer
     */
	public function getStatusCode()
	{
		return $this->status_code;
	}


    /**
     * devuelve array con el contenido del objeto
     *
     * @return  array
     */
	public function getArray()
	{
		return array(
				'meta'=>array(
					'code'=>$this->status_code,
					'message'=>$this->message,
				),
				'data'=>$this->data
		);
	}

    public function isOk()
    {
        return ($this->status_code >= 200) && ($this->status_code < 300);
    }

}
