<?php
namespace Navicu\Core\Domain\Model\ValueObject;

/**
 * la clase slug genera un string que sirve como slug para identificar un objeto
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho <kbo025@gmail.com>
 * @version 03/09/2015
 */
class Slug {

    /**
     *  string que contiene el slug
     *
     * @var string $slug
     */
    private $slug;

    /**
     * constructor de la clase
     */
    public function __construct($slug, $subSpace = '-')
    {
        $this->slug = self::generateSlug($slug, $subSpace);
    }

    /**
     * devuelve el slug como un string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return char
     */
    public function getSubSpace()
    {
        return $this->subSpace;
    }

    /**
     * @param char $subSpace
     */
    public function setSubSpace($subSpace)
    {
        $this->subSpace = $subSpace;
    }


    /**
     *   formatea un string y devuelve un slug valido
     */
    public static function generateSlug($slug, $subSpace = '-')
    {
        //reemplazando caracteres especiales por simples
        $slug = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $slug
        );

        $slug = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $slug
        );

        $slug = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $slug
        );

        $slug = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô','Õ','Ø'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O','O','O'),
            $slug
        );

        $slug = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $slug
        );

        $slug = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç','Š','š','Ž','ž','Ý','ý','ÿ'),
            array('n', 'N', 'c', 'C','S','s','Z','z','Y','y','y'),
            $slug
        );

        $slug = str_replace(
            array('’', '‘', '‹', '›'),
            array('',  '',  '',  ''),
            $slug
        );

        $slug = str_replace(
            array('“', '”', '«', '»', '„', ','),
            array('',  '',  '',  '', '',   ''),
            $slug
        );

        $slug = str_replace('.','',$slug);

        //convirtiendo todo a minusculas
        $slug = strtolower($slug);

        //eliminando el resto de los caracteres especiales
        $slug = preg_replace('/[^a-zA-Z0-9\s]-'.$subSpace.'/i','', $slug);

        //limpiando espacios en blancos en los extremos
        $slug = trim($slug);

        // Rellenamos espacios con guiones
        $slug = preg_replace('/\s+/', $subSpace, $slug);

        return $slug;
    }
} 