<?php

namespace Navicu\Core\Application\UseCases\Admin\Users\GetInfoUser;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreTranslator;


/**
 * Esta función Envia los datos  usuarios AAVV , client , admin , hotelero por perfil
 * @author Freddy Contreras <freddycontrerase3@gmail.com>
 * @version 09/06/2016
 */
class GetInfoUserHandler implements Handler
{
    private $rf;

    /**
     *  Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $request = $command->getRequest();
        $this->rf = $rf;
        $user = null;
        $response = ['userData' => null, 'departaments' => [], 'hobbies' => [],'countries' => []];

        if ($request['userId']) {
            if ($request['role'] == 'admin')
                $response['userData'] = $this->findNvcProfile($request['userId']);
            else if ($request['role'] == 'client') {
                $response['userData'] = $this->findClientProfile($request['userId']);
                $response['role'] = 21;
            } else if ($request['role'] == 'owner') {
                $response['userData'] = $this->findOwnerProfile($request['userId']);
                $response['role'] = 1;
            } else if ($request['role'] == 'aavv') {
                $response['userData'] = $this->findAAVVProfile($request['userId']);
                $response['role'] = 6;
            }
        }

        if ($request['role'] == 'admin' or $request['role'] == 'all') {
            $rpDepartament = $rf->get('Departament');
            $departaments = $rpDepartament->findBy(['role' => null]);

            foreach ($departaments as $currentDepartament) {
                $auxDepartament = [];
                $auxDepartament['id'] = $currentDepartament->getId();
                $auxDepartament['name'] = CoreTranslator::getTranslator($currentDepartament->getCode(),'departaments');
                $auxDepartament['roles'] = [];
                $auxChild = [];
                foreach ($currentDepartament->getChildren() as $currentChild) {
                    $auxChild['id'] = $currentChild->getRole();
                    $auxChild['name'] = CoreTranslator::getTranslator($currentChild->getCode(),'departaments');
                    $auxDepartament['users'][] = $auxChild;
                }
                $response['departaments'][] = $auxDepartament;
            }
        }

        $rpHobbies = $rf->get('Hobbies');
        $hobbies = $rpHobbies->findAll();
        $auxHobbie = [];

        foreach ($hobbies as $currentHobbie) {
            $auxHobbie['id'] = $currentHobbie->getId();
            $auxHobbie['name'] = $currentHobbie->getTitle();
            $response['hobbies'][] = $auxHobbie;
        }

        $rpLocation = $rf->get('Location');
        $countries = $rpLocation->findBy(['lvl' => 0]);
        $auxCountry = [];

        foreach ($countries as $currentCountry) {
            $auxCountry['id'] = $currentCountry->getId();
            $auxCountry['name'] = $currentCountry->getTitle();
            $response['countries'][] = $auxCountry;
        }

        return new ResponseCommandBus(200,'Ok', $response);
    }

    /**
     * Retorna los datos necesarios de un usuario
     * con perfil NvcProfile
     *
     * @param $userId
     * @return array
     * @author Freddy Contreras <freddycontreras>
     */
    private function findNvcProfile($userId)
    {
        $response = [];
        $nvcProfile = $this->rf->get('NvcProfile')->findOneBy(['id' => $userId]);

        if ($nvcProfile) {
            $response['id'] = $nvcProfile->getId();
            $response['username'] = $nvcProfile->getUser()->getUserName();
            $response['fullName'] = $nvcProfile->getFullName();
            $response['gender'] = $nvcProfile->getGender();
			$response['dateCreateUser'] = $nvcProfile->getJoinedDate();
			$response['identityCard'] = $nvcProfile->getIdentityCard();
            $response['corporateEmail'] = $nvcProfile->getCompanyEmail();
			$response['department'] = $nvcProfile->getDepartament()->getParent()->getId();
			$response['position'] = $nvcProfile->getDepartament()->getRole();
			$response['cellPhone'] = $nvcProfile->getCellPhone();
            $response['personalEmail'] = $nvcProfile->getPersonalEmail();
            $response['companyEmail'] = $nvcProfile->getUser()->getEmail();
            $response['corporatePhone'] = $nvcProfile->getCorporatePhone();
            $response['incorporationDate'] = $nvcProfile->getIncorporationDate();
            $response['treatment'] = $nvcProfile->getTreatment();
            $response['birthDate'] = $nvcProfile->getBirthDate();
        }

        return $response;
    }

    /**
     * Retorna los datos necesarios de un usuario
     * con perfil ClientProfile
     *
     * @param $userId
     * @return array
     * @author Freddy Contreras <freddycontreras>
     */
    private function findClientProfile($userId)
    {
        $response = [];
        $clientProfile = $this->rf->get('ClientProfile')->findOneBy(['id' => $userId]);

        if ($clientProfile) {
            $response['id'] = $clientProfile->getId();
            $response['username'] = $clientProfile->getUser()->getUserName();
            $response['treatment'] = $clientProfile->getTreatment();
            $response['fullName'] = $clientProfile->getFullName();
            if ($clientProfile->getTreatment())
                $response['treatment'] = $clientProfile->getTreatment();

            if ($clientProfile->getBirthDate()) {
                $response['birthDate'] = $clientProfile->getBirthDate();
            }
            if ($clientProfile->getGender())
                $response['gender'] = $clientProfile->getGender();

            $response['identityCard'] = $clientProfile->getIdentityCard();
            $response['emailNews'] = $clientProfile->getEmailNews();
            $response['email'] = $clientProfile->getUser()->getEmail();
            $response['company'] = $clientProfile->getCompany();
            $response['position'] = $clientProfile->getPosition();
            $response['phone'] = $clientProfile->getPhone();
            $response['joined_date'] = $clientProfile->getJoinedDate();
            $response['address'] = $clientProfile->getAddress();
            $response['zipCode'] = $clientProfile->getZipCode();

            if ($clientProfile->getLocation()) {
                $response['countryId'] = $clientProfile->getLocation()->getRoot()->getId();
                $response['stateId'] = $clientProfile->getLocation()->getId();
            }

            $auxHobbie = [];
            $response['hobbies'] = [];
            foreach ($clientProfile->getHobbies() as $currentHobbie) {
                //$auxHobbie['name'] = $currentHobbie->getTitle();
                $auxHobbie['id'] = (string)$currentHobbie->getId();
                $response['hobbies'][] = $auxHobbie;
            }
        }

        return $response;
    }

    /**
     * Retorna los datos necesarios de un usuario
     * con perfil OwnerProfile
     *
     * @param $userId
     * @return array
     * @author Freddy Contreras <freddycontreras>
     */
    private function findOwnerProfile($userId)
    {
        $response = [];
        $ownerProfile = $this->rf->get('OwnerProfile')->findOneBy(['id' => $userId]);

        if ($ownerProfile) {
            $response['id'] = $ownerProfile->getId();
            $response['username'] = $ownerProfile->getUser()->getUserName();
            if ($ownerProfile->getBirthDate())
                $response['birthDate'] = $ownerProfile->getBirthDate();
            $response['treatment'] = $ownerProfile->getTreatment();
            $response['identityCard'] = $ownerProfile->getIdentityCard();
            $response['emailNews'] = $ownerProfile->getEmailNews();
            $response['email'] = $ownerProfile->getUser()->getEmail();
            $response['company'] = $ownerProfile->getCompany();
            $response['position'] = $ownerProfile->getPosition();
            $response['gender'] = $ownerProfile->getGender();
            $response['fullName'] = $ownerProfile->getFullName();
            if ($ownerProfile->getPhones() and !empty($ownerProfile->getPhones()))
                $response['phone'] = json_decode($ownerProfile->getPhones())[0];

            if ($ownerProfile->getJoinedDate())
                $response['joinedDate'] = $ownerProfile->getJoinedDate()->format('Y-m-d');
        }

        return $response;
    }

    /**
     * Retorna los datos necesarios de un usuario
     * con perfil AAVVProfile
     *
     * @param $userId
     * @return array
     * @author Freddy Contreras <freddycontreras>
     */
    private function findAAVVProfile($userId)
    {
        $response = [];
        $aavvProfile = $this->rf->get('AAVVProfile')->findOneBy(['id' => $userId]);

        if ($aavvProfile) {
            $response['id'] = $aavvProfile->getId();
            $response['fullName'] = $aavvProfile->getFullName();
            $response['username'] = $aavvProfile->getUser()->getUserName();
            $response['email'] = $aavvProfile->getUser()->getEmail();
            $response['rif'] =  $aavvProfile->getRif();
            $response['managerEmail'] = $aavvProfile->getManagerEmail();
            $response['managerBirthDate'] = $aavvProfile->getManagerBirthDate();
            $response['localPhone'] = $aavvProfile->getLocalPhone();
            $response['managerName'] = $aavvProfile->getManagerName();
            $response['managerCell'] = $aavvProfile->getManagerCell();
            $response['corporatePhone'] = $aavvProfile->getCorporatePhone();
            $response['zipCode'] = $aavvProfile->getZipCode();
            $response['address'] = $aavvProfile->getAddress();
            $response['location'] = (string)$aavvProfile->getLocation()->getId();
            $response['stateId'] = $aavvProfile->getLocation()->getParent()->getId();
            $response['countryId'] = $aavvProfile->getLocation()->getRoot()->getId();
        }

        return $response;
    }
}