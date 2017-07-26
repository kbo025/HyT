<?php

namespace Navicu\Core\Application\UseCases\GetClientProfile;

use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Domain\Adapter\CoreTranslator;
use Navicu\Core\Domain\Model\Entity\ClientProfile;

/**
 * Created by Isabel Nieto.
 * User: user03
 * Date: 20/04/16
 * Time: 02:32 PM
 */
/**
 * Class EditClientProfileHandler
 * @package Navicu\Core\Application\UseCases\EditClientProfile
 */
class GetClientProfileHandler implements Handler
{
    /**
     *  Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $data = $command->getRequest();
        $clientToConsult = $data["clientProfile"];

        $response = [];
        $response['data'] = $this->getDataProfessionHobbies($clientToConsult);
        $response['professions'] = $this->getAllProfessions($rf, $response);
        $response['hobbies'] = $this->getAllHobbies($rf, $response);

        if ($data["clientProfile"])
            return new ResponseCommandBus(200, 'Ok', $response);
        else
            return new ResponseCommandBus(404, 'Not Found');
    }

    /**
     * Asigna la información del cliente nueva a guardar
     *
     * @param object $clientToConsult objeto que lleva la informacion nueva
     * @return array $response
     */
    public function getDataProfessionHobbies(ClientProfile $clientToConsult)
    {
        $response = $clientToConsult->toArray();
        $hobbies= $clientToConsult->getHobbies();
        $professions = $clientToConsult->getProfessions();

        $list_hobbies = [];
        $list_profession = [];
        $response['hobbies'] = [];
        $response['professions'] = [];

        $lengthOfHobbies = count($hobbies);
        for ($i = 0; $i < $lengthOfHobbies; $i++){
            $list_hobbies = $hobbies[$i]->getId();
            array_push($response['hobbies'], $list_hobbies);
        }

        $lengthOfProfessions = count($professions);
        for ($i = 0; $i < $lengthOfProfessions; $i++){
            $list_profession = $professions[$i]->getId();
            array_push($response['professions'], $list_profession);
        }
        return $response;
    }

    /***
     * Las profesiones existentes en la tabla de profesiones
     *
     * @param RepositoryFactoryInterface $rf
     * @return array $response almacenando todas las profesiones existentes
     */
    public function getAllProfessions(RepositoryFactoryInterface $rf)
    {
        $response = [];
        $list_profession = [];
        $professionsRep = $rf->get("Profession");
        $listOfProfession = $professionsRep->getAll();

        $length = count($listOfProfession);

        for ($i = 0; $i < $length; $i++)
        {
            $list_profession['id'] = $listOfProfession[$i]->getId();
            $list_profession['title'] = $listOfProfession[$i]->getTitle();
            array_push($response,$list_profession);
        }
        return $response;
    }

    /***
     * Los hobbies existentes en la tabla de hobbies
     *
     * @param RepositoryFactoryInterface $rf
     * @return array $response almacenando todas los hobbies existentes
     */
    public function getAllHobbies(RepositoryFactoryInterface $rf)
    {
        $list_hobbies = [];
        $response = [];
        $professionsRep = $rf->get("Hobbies");
        $listOfHobbies = $professionsRep->getAll();

        $length = count($listOfHobbies);

        for ($i = 0; $i < $length; $i++)
        {
            $list_hobbies['id'] = $listOfHobbies[$i]->getId();
            $list_hobbies['title'] = $listOfHobbies[$i]->getTitle();
            array_push($response,$list_hobbies);
        }
        return $response;
    }
}