<?php
namespace Navicu\Core\Domain\Adapter;

use Navicu\Core\Domain\Contract\Translator;

/**
* La clase siguiente hace uso del conntenedor de servicio para
* hacer llamado al servicio de translator de symfony2.
*
* @author Joel D. Requena P. <Joel.2005.2@gmail.com>
* @author Currently Working: Joel D. Requena P.
*/
class CoreTranslator implements Translator
{
    /**
     * Metodo que hace uso del trans del bundle de translator
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @param string $code
     * @param string $file
     */
    public static function getTranslator($code, $file = 'messages')
    {
        global $kernel;
        $locale = $kernel->getContainer()->get('session')->get('_locale');
        return $kernel->getContainer()->get('translator')->trans($code, array(), $file, $locale);
    }

    /**
     * Metodo que hace uso del TransChoice del bundle de translator
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @param string $code              Codigo de translator
     * @param string $n                 Identificador de la clave de traducción  '{0} or {1} or {n}
     * @param string $paramter          Parametros manejados dentro de la traducción
     * @param string $file              Tipo de archivo implementado en la traducción
     */
    public static function getTransChoice($code, $n, $paramter = [], $file = 'messages')
    {
        global $kernel;
        $locale = $kernel->getContainer()->get('session')->get('_locale');
        return $kernel->getContainer()->get('translator')->transChoice($code, $n, $paramter, $file, $locale);
    }
}