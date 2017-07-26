<?php
/**
 * Created by Isabel Nieto.
 * User: root
 * Date: 15/04/16
 * Time: 12:14 PM
 */
namespace Navicu\Core\Application\UseCases\EditClientProfile;

use Doctrine\Common\Collections\ArrayCollection;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Domain\Model\Entity\ClientProfile;
use Navicu\Core\Domain\Model\ValueObject\Phone;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Adapter\CoreValidator;

/**
 * Class EditClientProfileHandler
 * @package Navicu\Core\Application\UseCases\EditClientProfile
 */
class EditClientProfileHandler implements Handler
{
    /**
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $data = $command->getRequest();

        $rpClientProfile = $rf->get("ClientProfile");
        $clientToUpdate = $data["client_profile"];

        if (is_null($clientToUpdate))
            return new ResponseCommandBus(400, 'No se encontro el usuario solicitado');

        try{
            $phone = new Phone($data["phone"]);
        } catch(\Exception $e) {
            return new ResponseCommandBus(400,'Bad Request',['internal'=>$e->getMessage(),'message' =>  'Número de teléfono inválido, por favor verifica e intenta de nuevo']);
        }

        try{
            $email = new EmailAddress($data["email"]);
        } catch(\Exception $e) {
            return new ResponseCommandBus(400,'Bad Request',['internal'=>$e->getMessage(), 'message' => 'Dirección de correo electrónico inválido, por favor verifica e intenta de nuevo']);
        }

        $data["email"] = $email;
        $data["phone"] = $phone;

        $clientToUpdate->updateObject($data);

        $clientToUpdate->removeAllProfessions();
        $this->addProfessions($data, $rf, $clientToUpdate);

        $clientToUpdate->removeAllHobbies();
        $this->addHobbies($data, $rf, $clientToUpdate);

        $validator = CoreValidator::getValidator($clientToUpdate);

        if (count($validator))
            return new ResponseCommandBus(400, 'Bad Request', $validator);
        $rpClientProfile->save($clientToUpdate);

        return new ResponseCommandBus(201, 'Ok');
    }

    /**
     * Generamos la coleccion de profesiones y se las asignamos a la del usuario
     *
     * @param object $data es la informacion nueva del usuario
     * @param $rf
     * @param object $clientToUpdate es el usuario que se esta actualizando
     */
    public function addProfessions($data, $rf, $clientToUpdate) {
        $profession = $rf->get("Profession");
        $professionOfClients = $data['professions'];
        $length = count($professionOfClients);

        /* Insertamos en la tabla de clientes las nuevas professiones correspondientes */
        for ($ii = 0; $ii < $length; $ii++) {
            $newProfession = $profession->findOneByArray(array('id' => $professionOfClients[$ii]));

            /*Actualizamos las profesiones del cliente*/
            $clientToUpdate->addProfession($newProfession);

            /* Insertamos en la tabla de professiones los clientes que las utilizan */
            $newProfession->addClient($clientToUpdate);
        }
    }

    /**
     * Generamos la coleccion de hobbies y se las asignamos al usuario
     *
     * @param object $data es la informacion nueva del usuario
     * @param $rf
     * @param object $clientToUpdate es el usuario que se esta actualizando
     *
     */
    public function addHobbies($data, $rf, $clientToUpdate)
    {
        $hobbies = $rf->get("Hobbies");
        $hobbiesOfClients = $data["hobbies"];
        $length = count($hobbiesOfClients);

        for ($ii = 0; $ii < $length; $ii++) {
            $newHobbies = $hobbies->findOneByArray(array('id' => $hobbiesOfClients[$ii]));

            $clientToUpdate->addHobby($newHobbies);

            $newHobbies->addClient($clientToUpdate);
        }
    }
}