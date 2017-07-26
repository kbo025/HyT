<?php
namespace Navicu\Core\Domain\Adapter;

use Navicu\Core\Domain\Contract\Validator;
use Navicu\Core\Domain\Adapter\CoreTranslator;
/**
* La clase siguiente hace uso del conntenedor de servicio para
* hacer llamado al servicio de validator de symfony2.
*
* @author Joel D. Requena P. <Joel.2005.2@gmail.com>
* @author Currently Working: Joel D. Requena P.
*/
class CoreValidator implements Validator
{
    /**
     * Metodo que hace uso del validator del framework.
     * 
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @param object $obj
	 */
    public static function getValidator($obj, $critery = null)
    {
        global $kernel;
        $error = $kernel->getContainer()->get('validator')->validate($obj, $critery);

        if (count($error) > 0) {
            $response = array(); global $kernel;
		$locale = $kernel->getContainer()->get('session')->get('_locale');
			$translator = new CoreTranslator();

            for ($i = 0; $i < count($error); $i++) {
                $message = explode( ',',$error[$i]->getMessage());
                $value = preg_replace('/[^A-Za-z0-9\-]/', '', $message[1]);

                // Pasar camel_case a camelCase
                // Comentado debido a que las variables de comunicación
                // en los casos de usos (command) pueden tener _
//                $propertyPath = str_replace('_', ' ', $error[$i]->getPropertyPath());
//                $propertyPath = lcfirst(ucwords($propertyPath));
//                $propertyPath = str_replace(' ', '', $propertyPath);

                // Guardar en el array de respuesta
                array_push(
                    $response,
                    array(
                        "message" => $translator->getTranslator($message[0]),
                        "value" => $value,
                        "parameter" => $error[$i]->getPropertyPath()
                    )
                );
            }

            return $response;
        }

        return null;
    }
}