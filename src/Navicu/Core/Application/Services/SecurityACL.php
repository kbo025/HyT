<?php 

namespace Navicu\Core\Application\Services;

use Navicu\Core\Domain\Adapter\CoreSession;
/**
 * Clase SecurityACL
 *
 * Se define una clase y una serie de funciones necesarios para el manejo de
 * las reglas ACL aplcadas cobre los objetos.
 * 
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 * 
 */
class SecurityACL
{
    /**
     * Esta función es usada para comparar el slug enviado por el usuario
     * con el slug que guarda el recurso solicitado por el mismo.
     * 
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     * 
     * @param String $slug
     * @param Object $ownerProfile
     * @return Boolean
     */
    public static function isSlugOwner($slug, $ownerProfile)
    {
        if (CoreSession::isRole('ROLE_ADMIN') or CoreSession::isRole('ROLE_DIR_COMMERCIAL') or
            CoreSession::isRole('ROLE_SALES_EXEC') or CoreSession::isRole('ROLE_TELEMARKETING'))
            return true;

        if (!empty($ownerProfile)) {
            $ban = false;
            $properties = $ownerProfile->getProperties();
            for ($i = 0; $i < count($properties); $i++) {
                if(strtolower($properties[$i]->getSlug()) == strtolower($slug)) {
                    $ban = true;
                    break;
                }
            }
            return $ban;
        } else {
            return null;
        }
    }
    
}