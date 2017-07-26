<?php
namespace Navicu\Core\Application\Contract;


class CommandBase
{
    public function __construct($data = null)
    {
        if (!empty($data) && is_array($data)) {
            $this->setAtrributes($data);
        }
    }

    public function get($att)
    {
        return ( isset($this->$att) ? $this->$att : null );
    }

    public function set($att,$val)
    {
        $this->$att = $val;

        return $this;
    }

    public function setAtrributes($data)
    {
        if (is_array($data)) {
            foreach($data as $att => $val) {
                $this->set($att,$val);
            }
            return true;
        }
        return false;
    }

    /**
     *  metodo getRequest devuelve un array con los parametros del command
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 05-05-2015
     * @return  Array
     */
    public function getRequest()
    {
        $response = [];
        foreach($this as $att => $val)
        {
            $response[$att] = $val;
        }
        return $response;
    }
}