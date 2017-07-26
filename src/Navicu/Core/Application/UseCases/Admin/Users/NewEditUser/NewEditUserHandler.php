<?php
namespace Navicu\Core\Application\UseCases\Admin\Users\NewEditUser;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\EmailInterface;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Model\Entity\AAVVProfile;
use Navicu\Core\Domain\Adapter\CoreValidator;
use Navicu\Core\Domain\Adapter\CoreUser;
use Navicu\Core\Domain\Model\Entity\ClientProfile;
use Navicu\Core\Domain\Model\Entity\NvcProfile;
use Navicu\Core\Domain\Model\Entity\OwnerProfile;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;

/**
 * La clase se encarga de crear o editar un usuario en el sistema
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 31/05/2016
 */
class NewEditUserHandler implements Handler
{

    private $emailService;

    /**
     * Método Get del la interfaz del serivicio Email
     * @internal param EmailInterface $emailService
     * @return \Navicu\Core\Application\Contract\EmailInterface
     */
    public function getEmailService()
    {
        return $this->emailService;
    }

    /**
     * Método Set del la interfaz del serivicio Email
     * @param EmailInterface $emailService
     */
    public function setEmailService(EmailInterface $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Esta funcion crea y edita usuarios AAVV,client,admin,hotelero por perfil
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 25/06/2016
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $request = $command->getRequest();

        $requestEmail = isset($request['data']["email"]) ? $request['data']["email"] :
            (isset($request['data']["emailCompany"]) ? $request['data']["emailCompany"] : null);

        /* Existe usuario por el Email y se envio una al registro */
        $existUser = $rf->get("User")->findOneByArray(["email" => $requestEmail]);

        /**Existe usuario con el Email*/
        if (isset($existUser) and !$request['data']['id'])
            return new ResponseCommandBus(
                404, 'Bad Request', ["message" => "El correo electrónico registrado previamente", "value" => $requestEmail, "parameter" => "email", "exists" => true]);

        try {
            if (isset($request['data']['email']))
                $request['data']['email'] = new EmailAddress($request['data']['email']);
            else if (isset($request['data']['emailCompany']))
                $request['data']['email'] = new EmailAddress($request['data']['emailCompany']);

        } catch (\Exception $e) {
            return new ResponseCommandBus(400, 'Bad request', $e->getMessage());
        }

        $profile = null;
        $emailData = [];

        if (isset($request['data']['location']) and $request['data']['location'])
            $request['data']['location'] = $rf->get('Location')->findOneBy(['id' => $request['data']['location']]);

        switch ($request['data']['role']) {

            case 6:
                /** ingreso al perfil agente viajero y creo el usuario*/

                if (!isset($request['data']['id']))
                    $profile = new AAVVProfile();
                else {
                    $rpProfile = $rf->get('AAVVProfile');
                    $profile = $rpProfile->findOneByArray(['id' => $request['data']['id']]);
                }

                if (isset($request['data']['location']) and $request['data']['location'])
                    $request['data']['location'] = $rf->get('Location')->findOneBy(['id' => $request['data']['location']]);

                break;

            /** Crea perfil segun su rol admin*/
            case 5:
            case 15:
            case 16:
            case 7:
            case 8:
            case 9:
            case 10:
            case 11:
            case 12:
            case 13:
            case 14:
            case 22:
            case 23:
            case 17:
            case 18:
            case 19:
            case 20:
            case 22:
            case 23:

                if (!isset($request['data']['id']))
                    $profile = new NvcProfile();
                else {
                    $rpProfile = $rf->get('NvcProfile');
                    $profile = $rpProfile->findOneByArray(['id' => $request['data']['id']]);
                }

                // Asignación de dapartamento
                $rpDepartamet = $rf->get('Departament');
                $departament = $rpDepartamet->findOneBy(['role' => $request['data']['position']]);
                if ($departament)
                    $profile->setDepartament($departament);

                $request['data']['email'] = new EmailAddress($request['data']['companyEmail']);
                break;
            case 21:

                if (!isset($request['data']['id']))
                    $profile = new ClientProfile();
                else {
                    $rpProfile = $rf->get('ClientProfile');
                    $profile = $rpProfile->findOneByArray(['id' => $request['data']['id']]);
                }

                if (isset($request['data']['hobbies'])) {
                    $profile->removeAllHobbies();
                    $rpHobbie = $rf->get('Hobbies');
                    foreach ($request['data']['hobbies'] as $currentHobbie) {
                        $hobbie = $rpHobbie->findOneBy(['id' => $currentHobbie['id']]);
                        if ($hobbie)
                            $profile->addHobby($hobbie);
                    }
                }

                break;
            case 1:
                if (!isset($request['data']['id']))
                    $profile = new OwnerProfile();
                else {
                    $rpProfile = $rf->get('OwnerProfile');
                    $profile = $rpProfile->findOneBy(['id' => $request['data']['id']]);
                }
                break;
        }

        // Actualizando los datos de perfil
        $profile->updateObject($request['data']);
        /** valido el perfil que estoy ingresando*/
        $validator = CoreValidator::getValidator($profile);

        if ($validator)
            return new ResponseCommandBus(
                404, null,
                ['validates' => $validator, 'message' => 'Hubo un error en la solicitud por favor verifica tus datos e intenta de nuevo o comunicate con nosotros']);
        else {
            if (isset($request['data']['username']))
                $username = $request['data']['username'];
            else
                /** si es un usuario nuevo asigno un nombre de usuario desde el correo ingresado*/
                $username = \explode('@', $request['data']['email']->toString())[0];

            $i = 1;
            do {
                $auxUser = $rf->get("User")->findOneByArray(["username" => $username]);
                if ($auxUser and !isset($request['data']['id'])) {
                    $username = $username . $i;
                    $i++;
                } else
                    $auxUser = null;
            } while (!is_null($auxUser));
        }

        $request['data']['username'] = $username;
        $user = CoreUser::setEditCreatUser($request['data'], $profile);
        if (!isset($request['data']['id'])) {
            $emailData['username'] = $user->getUsername();
            $emailData['fullName'] = $profile->getFullName();
            $emailData['password'] = $user->getPlainPassword();
            $emailData['email'] = $requestEmail;
            $this->sendEmailData($emailData);
        }

        CoreUser::save($user, $rf);
        return new ResponseCommandBus(200, null);
    }

    /**
     * Envio de correo de confirmación de registro al cliente
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param array $emailData
     * @return bool|string
     */
    public function sendEmailData($emailData)
    {
        $emailService = $this->getEmailService();
        $emailService->setConfigEmail('first_mailer');

        $emailService->setRecipients([$emailData['email']]);
        $emailService->setViewParameters($emailData);
        $emailService->setViewRender('NavicuInfrastructureBundle:Email:singInClient.html.twig');
        $emailService->setSubject('Bienvenido a navicu.com - Datos del registro');
        $emailService->setEmailSender('info@navicu.com');
        $emailService->sendEmail();
    }
}