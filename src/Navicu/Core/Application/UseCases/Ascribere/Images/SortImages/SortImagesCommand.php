<?php
namespace Navicu\Core\Application\UseCases\Ascribere\Images\SortImages;

use Navicu\Core\Application\Contract\Command;

/**
 * La siguiente clase se encarga de Caso de uso donde
 * se orden las imagenes del establecimiento en proceso de registro
 *
 * Class SortImagesCommand
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 21/10/2015
 */
class SortImagesCommand implements Command
{
    /**
     * @var representa el slug del establecimiento
     */
    private $slug;

    /**
     * @var representa el json con los datos del ordenamiento
     */
    private $data;

    public function __construct($slug = null , $data = null)
    {
        $this->slug = $slug;
        $this->data = $data;
    }

    public function getRequest()
    {
        return array(
            'slug' => $this->slug,
            'data' => $this->data,
        );
    }

    /**
     *  Método get devuelve el atributo del comando que se pasa por parametro
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param String $att
     * @version 13/09/2015
     * @return  mixed
     */
    public function get($att)
    {
        if(isset($this->$att))
            return $this->$att;
        else
            throw new \Exception('The class '.get_class($this).' not contains the attribute '.$att);
    }

    /**
     *  Método actualiza el atributo del comando dado un string que representa el nombre atributo
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param String $att
     * @param mixed $value
     * @version 13/09/2015
     */
    public function set($att, $value)
    {
        if(isset($this->$att))
            $this->$att = $value;
        else
            throw new \Exception('The class '.get_class($this).' not contains the attribute '.$att);
    }
}