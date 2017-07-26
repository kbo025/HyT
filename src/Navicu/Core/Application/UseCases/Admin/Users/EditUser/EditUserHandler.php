<?php
namespace Navicu\Core\Application\UseCases\Admin\Users\EditUser;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Model\Entity\AAVVProfile;
use Navicu\Core\Domain\Model\Entity\ClientProfile;
use Navicu\Core\Domain\Model\Entity\NvcProfile;
use Navicu\Core\Domain\Model\Entity\OwnerProfile;
use Navicu\Core\Domain\Adapter\CoreValidator;
use Navicu\Core\Domain\Adapter\CoreUser;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;

/**
 * Conjunto de objetos para editar los valores ingresados del usuario por perteneciente al caso de uso NewUserEdit
 * @author	Mary sanchez
 * @version 09-06-2016
 */
class EditUserHandler implements Handler
{

    /**
     * Esta funcion ejecuta la opcion de editar campo en cada perfil de usuario
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $data = $command->getRequest();
        $role = $data['data']['role'];
        $stringEmail = $data['data']['email'];

        switch ($role) {
            case 6: /** Editar  perfil agente viajero */
                $rpProfile = $rf->get('AAVVProfile');
                $profile = $rpProfile->findOneBy(['id' => $data['data']['Id']]);
                if (isset($profile)) {
                    $profile->updateObject($data['data']);
                    $rpProfile->save($profile);
                }
            break;
            /** Editar perfil segun su rol administrador*/

            case 15: case 16:case 7:case 8:case 9:case 10:
            case 11:case 12:case 13:case 14:case 22:case 23:
            case 17:case 18:case 19: case 20:

            $rpProfile = $rf->get('NvcProfile');
            $profile = $rpProfile->findOneBy(['id' => $data['data']['Id']]);
            if (isset($profile)) {
                $profile->updateObject($data['data']);
                $rpProfile->save($profile);
            }
            break;
            case 21:
                $rpProfile = $rf->get('ClientProfile');
                $profile = $rpProfile->findOneBy(['id' => $data['data']['Id']]);
                $data['data']['email'] = new EmailAddress($data['data']['email']);
                if (isset($profile)) {
                    $profile->updateObject($data['data']);
                    $rpProfile->save($profile);
                }
            break;
            case 1:
                $rpProfile = $rf->get('OwnerProfile');
                $profile = $rpProfile->findOneBy(['id' => $data['data']['Id']]);
                if (isset($profile)) {
                    $profile->updateObject($data['data']);
                    $rpProfile->save($profile);
                }
            break;
        }

       $validator = CoreValidator::getValidator($profile); /** valido el perfil que estoy ingresando*/

        if ($validator)
            return new ResponseCommandBus(
                400,
                null,
                [   'validates' => $validator,
                    'message' => 'Hubo un error en la solicitud por favor verifica tus datos e intenta de nuevo sino comunicate con nosotros']);

           return new ResponseCommandBus(200, null,['ok']);

    }
}