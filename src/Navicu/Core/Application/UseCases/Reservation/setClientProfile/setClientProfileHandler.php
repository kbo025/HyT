<?php

namespace Navicu\Core\Application\UseCases\Reservation\setClientProfile;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Model\Entity\ClientProfile;
use Navicu\Core\Domain\Model\Entity\RedSocial;
use Navicu\Core\Domain\Model\ValueObject\Phone;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Adapter\CoreValidator;
use Navicu\Core\Domain\Adapter\CoreUser;
use Navicu\Core\Application\Contract\EmailInterface;

/**
 * El siguiente handler maneja la creación de un perfil de cliente.
 *
 * Class setClientProfile
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 */
class setClientProfileHandler implements Handler
{

    protected $rf;

    /**
     * La variable contiene la funcionalidades del servicio de email
     *
     * @var EmailInterface $emailService
     */
    protected $emailService;

    /**
     * Método Get del la interfaz del serivicio Email
     * @param EmailInterface $emailService
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
     * Ejecuta las tareas solicitadas
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     *
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf = null)
    {
        $request = $command->getRequest();
        $request["birthDate"] = null;

        #Existe Cliente por el Email y se envio una red social
        $user = $rf->get("User")->findOneByArray(["email"=>$request["email"]]);
        if($user)
            $existClient = $user->getClientProfile();

        if (isset($existClient) and $request["redSocial"]){
            $addSocial = $this->addSocial($existClient, $request["redSocial"], $rf);
            $existClient->setIdentityCard($request["identityCard"]);
            if (isset($request["typeIdentity"]))
                $existClient->setTypeIdentity($request["typeIdentity"]);
            //if ($addSocial)
                $rf->get("ClientProfile")->save($existClient);

            return new ResponseCommandBus(201, null, true);
        }

        #Existe Cliente por el Email
        if (isset($existClient)) {
            if (isset($request["typeIdentity"]))
                $existClient->setTypeIdentity($request["typeIdentity"]);
            $rf->get("ClientProfile")->save($existClient);
            return new ResponseCommandBus(401, 'Ok', ["message"=>"exist.email", "value"=>$request["email"], "parameter"=>"email","exists"=>true]);
        }


        try{
            $phone = new Phone($request["phone"]);
        } catch(\Exception $e) {
            return new ResponseCommandBus(400,'Bad Request',['internal'=>$e->getMessage(),'message' =>  'Número de teléfono invalido, por favor verifica e intenta de nuevo']);
        }

        try{
            $email = new EmailAddress($request["email"]);
        } catch(\Exception $e) {
            return new ResponseCommandBus(400,'Bad Request',['internal'=>$e->getMessage(), 'message' => 'Dirección de correo electrónico invalido por favor verifica e intenta de nuevo']);
        }

        $country = isset($request["country"]) ? $request["country"]: 1;
        $state = isset($request["state"]) ? $request["state"]: null;
        $client = new ClientProfile();
        $client->setFullName($request["fullName"]);
        $client->setIdentityCard($request["identityCard"]);
        $client->setCountry($country);
        $client->setState($state);
        if (isset($request["typeIdentity"]))
            $client->setTypeIdentity($request["typeIdentity"]);

        if (isset($request['state'])) {
            $rpLocation = $rf->get('Location');
            $location = $rpLocation->findOneBy(['id' => $request['state']]);
            if ($location)
                $client->setLocation($location);
        }

        try{
            $client->setGender($request["gender"]);
        } catch(\Exception $e) {
			return new ResponseCommandBus(400,null,['message'=>'Hubo un error en la solicitud por favor verifica tus datos e intenta de nuevo o comunicate con nosotros']);
		}

        $client->setEmail($email);
        $client->setPhone($phone);
        $client->setBirthdate($request["birthDate"]);
        $validator = CoreValidator::getValidator($client);

        if ($validator)
            return new ResponseCommandBus(400,null, ['validates' => $validator, 'message' => 'Hubo un error en la solicitud por favor verifica tus datos e intenta de nuevo o comunicate con nosotros']);

        if ($request["redSocial"]) {
            $redSocial = new RedSocial();
            $redSocial->updateObject($request["redSocial"], $client);
        }
        // Persist fechar con formato de datetime.
        $client->setBirthdate(new \DateTime ($request["birthDate"]));
        if(!$user) {
            if (isset($request["pass1"],$request["pass2"]) && $request["pass1"]!=$request["pass2"])
                return new ResponseCommandBus(400, 'Bad Request', ['message'=>'La contraseña y su confirmación no coinciden']);
            else {
                try {
                    $username = \explode('@',$request['email']);
                    $username = $username[0];
                    $i = 2;
                    do {
                        $auxUser = $rf->get("User")->findOneByArray(["username"=>$username]);
                        if(!is_null($auxUser)) {
                            $username = $username . $i;
                            $i++;
                        }
                    } while(!is_null($auxUser));
                    $request['username'] = $username;
                    $user = CoreUser::setUser($request, $client);
                } catch(\Exception $e) {
                    return new ResponseCommandBus(400,'Bad Request',['internal'=>$e->getMessage() . $e->getFile(),'message' =>  'Tu password debe contener un mínimo de 6 caracteres entre números y letras']);
                }
            }
        } else {
            $client->setUser($user);
        }
        $user->setClientProfile($client);

        $dataEmail['email'] = $client->getEmail()->toString();
        $dataEmail['password'] = $user->getPlainPassword();
        $dataEmail['fullName'] = $client->getFullName();
        $dataEmail['generatedPassword'] = isset($request["pass1"],$request["pass2"]) ? false : true;

        CoreUser::save($user, $rf);
        if (!$command->isToAavv())
            $this->sendEmailData($dataEmail);

        return new ResponseCommandBus(201, null, true);
    }

    /**
     * Esta función es usada para incluir dentro de las redes sosciales
     * de un usuario el manejo de mas redes sociales.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param Command $command
     * @param Array $request
     * @param RepositoryFactoryInterface $rf
     *
     * @return boolean
     */
    public function addSocial($client, $request, $rf)
    {
        $social = $client->getSocial();
        $ban = 0;
		for ($s = 0; $s < count($social); $s++) {
            if ($social[$s]->getIdSocial() == $request["idSocial"] or $social[$s]->getType() == $request["type"])
                $ban = 1;
        }

        if ($ban == 0) {
            $redSocial = new RedSocial();
            $redSocial->updateObject($request, $client);
            return true;
        } else {
            return false;
        }
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

        try {
            $emailService = $this->getEmailService();
            $emailService->setConfigEmail('first_mailer');

            $emailService->setRecipients([$emailData['email']]);
            $emailService->setViewParameters($emailData);
            $emailService->setViewRender('NavicuInfrastructureBundle:Email:singInClient.html.twig');
            $emailService->setSubject('Bienvenido a navicu.com - Datos del registro');
            $emailService->setEmailSender('info@navicu.com');
            $emailService->sendEmail();
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return true;
    }
}