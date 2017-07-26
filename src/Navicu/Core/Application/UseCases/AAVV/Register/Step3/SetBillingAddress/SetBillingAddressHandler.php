<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 13/09/16
 * Time: 03:56 PM
 */

namespace Navicu\Core\Application\UseCases\AAVV\Register\Step3\SetBillingAddress;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\UseCases\AAVV\Register\Step4\ValidateRegistration\ValidateRegistrationHandler;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Adapter\CoreTranslator;
use Navicu\Core\Domain\Adapter\CoreValidator;
use Navicu\Core\Domain\Adapter\EntityValidationException;
use Navicu\Core\Domain\Model\Entity\AAVVAddress;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Model\ValueObject\Phone;

class SetBillingAddressHandler implements handler
{
    private $managerBD;

    /**
     * Utilizado para realizar un salvado o borrado masivo al instanciar dicha variable
     *
     * @param $managerBD
     */
    public function setManagerBD($managerBD)
    {
        $this->managerBD = $managerBD;
    }
    /**
     *  Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        try {
            $request = $command->getRequest();
            $response = $this->createNewBillingAddress($request, $rf);

            return $response;
        } catch (EntityValidationException $e) {
            return new ResponseCommandBus(500, $e->getMessage()."\n".$e->getLine());
        }
    }

    /**
     * Funcion encargada de generar o actualizar una direccion de cobro de la agencia de viajes
     *
     * @param $request
     * @param $rf
     *
     * @version 13/10/2016
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @return ResponseCommandBus
     */
    public function createNewBillingAddress($request, $rf)
    {
        $aavvRf = $rf->get("AAVV");
        $aavvAddressRf = $rf->get("AAVVAddress");
        $locationsRf = $rf->get("Location");
        $found = false;

        $aavv = $aavvRf->findOneByArray(["slug"=>$request['aavv']]);
        $locations = $locationsRf->find(isset($request['location']) ? $request['location'] : 1);
        $aavvAddresses = $aavv->getAavvAddress();

        $request['location'] = $locations;
        $request['aavv'] = $aavv;

        // Actualizamos las quotas que tiene la agencia de viajes y agregamos la del correo e interface de ser necesario
        $this->updateQuota($aavv, $rf, $request['personalized_mail'], $request['personalized_interface']);

        // Si ya han sido agregadas direcciones se revisa si hay una de cobro
        if (count($aavvAddresses) > 0) {
            foreach ($aavvAddresses as $address) {
                if ($address->getTypeAddress() == 2) {
                    $aavv->setPersonalizedMail($request['personalized_mail']);
                    $aavv->setPersonalizedInterface($request['personalized_interface']);
                    if ( !is_null($request['rif']) )
                        $aavv->setRif($request['rif']);


                    $address->updateObject($request);
                    // Todo: campos por rellenar cuando se trabajen las auditorias
//                    $aavv->setUpdatedBy(CoreSession::getUser());
//                    $today = new \DateTime("now");
//                    $aavv->setUpdatedAt($today);

                    $aavvRf->persistObject($aavv);
                    $aavvAddressRf->persistObject($address);
                    $found = true;
                }
            }
        }

        if ($found)
            $billingAddress = $this->responseStructure($address);
        // Si no se encontro la direccion de cobro entonces se genera una nueva
        else {
            $newAddress = new AAVVAddress();

            $newAddress->updateObject($request);
            $aavv->addAavvAddress($newAddress);
            $aavv->setPersonalizedMail($request['personalized_mail']);
            if ( !is_null($request['rif']) )
                $aavv->setRif($request['rif']);
            $aavv->setPersonalizedInterface($request['personalized_interface']);

            $aavvRf->persistObject($aavv);
            $aavvAddressRf->persistObject($newAddress);

            $billingAddress = $this->responseStructure($newAddress);
        }
        $aavvRf->flushObject();

        // Obtenemos el listado de quotas
        $quota = $this->getQuota($aavv, $rf);

        $response["quota_additional"] = $quota['quota'];
        $response["personalized_mail"] = $quota['personalized_mail'];
        $response["personalized_interface"] = $quota['personalized_interface'];
        $response['billing_address'] = $billingAddress;

        $validationResponse = ValidateRegistrationHandler::getValidations($aavv, $rf,3);
        return new ResponseCommandBus(200, 'ok', [
            "data" => $response,
            "validations" => $validationResponse->getData()
        ]);
    }

    /**
     * Funcion encargada de responder con la informacion guardada.
     *
     * @param $address object, nueva direccion creada
     * @return mixed
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 22/09/2016
     */
    public function responseStructure($address)
    {
        $aavv = $address->getAavv();

        $parentLocation = $location = $address->getLocation();
        // Recorremos los locations existentes y devolvemos los que encontremos
        if (!is_null($parentLocation)) {
            $position = 1;
            do {
                $parentLocation = $parentLocation->getParent();
                if (!is_null($parentLocation))
                    $position++;
            } while ((!is_null($parentLocation)) AND ($parentLocation->getLvl() > 0));
            switch ($position) {
                case 1: //Pais
                    $response['country'] = $location->getId(); // Pais
                    break;
                case 2: // Estado y Pais
                    $response['state'] = $location->getId(); // Estado
                    $response['country'] = $location->getParent()->getId(); // Pais
                    break;
                case 3:
                    $response['municipality'] = $location->getId(); // Municipio
                    $response['state'] = $location->getParent()->getId(); //Estado
                    $response['country'] = $location->getParent()->getParent()->getId(); // Pais
                    break;
                case 4:
                    $response['parish'] = $location->getId(); // Parroquia
                    $response['municipality'] = $location->getParent()->getId(); // Municipio
                    $response['state'] = $location->getParent()->getParent()->getId(); //Estado
                    $response['country'] = $location->getParent()->getParent()->getParent()->getId(); // Pais
                    break;
            }
        }

        $response['email'] = $address->getEmail();
        $response['bank_account'] = $address->getBankAccount();
        $response['phone'] = $address->getPhone();
        //$response['swift'] = $address->getSwift();
        $response['address'] = $address->getAddress();
        $response['zip_code'] = $address->getZipCode();
        $response['rif'] = $aavv->getRif();
        $response['social_reason'] = $aavv->getSocialReason();

        return $response;
    }

    /**
     * Funcion encargada de devolver el listado de quoatas existentes en la base de datos de la aavv
     *
     * @param $aavv
     * @param $rf
     * @return array
     */
    public function getQuota($aavv, $rf)
    {
        $quota = [];
        $personalized_mail = false;
        $personalized_interface = false;

        // Listado de quotas existentes en la BD
        $additionalQuotasOfAavv = $aavv->getAdditionalQuota();
        foreach ($additionalQuotasOfAavv as $Quota) {
            $aux["id"] = $Quota->getId();

            $aux["description"] = CoreTranslator::getTransChoice(
                "aavv.additionalQuota.".$Quota->getDescription(),
                $aavv->getAavvProfile()->count() > 1 ? 1 : 0
            );

            if ($Quota->getDescription() == "licence") {
                $totalLicences = ($aavv->getAavvProfile()->count() - 1);
                $amount = $Quota->getAmount() * $totalLicences;
                $aux["amount"] = number_format($amount,0,',','.');
                $name[$Quota->getDescription().'_number'] = ($aavv->getAavvProfile()->count() == 0)
                    ? 1
                    : $aavv->getAavvProfile()->count();
            } else {
                $aux["amount"] = number_format($Quota->getAmount(),0,',','.');
            }

            // Buscamos si tiene una quota agregada y alguna es "email"
            if (strcmp($Quota->getDescription(), 'email') == 0)
                $personalized_mail = true;
            // Buscamos si tiene una qouta agregada y alguna es "interface"
            if (strcmp($Quota->getDescription(), 'interface') == 0)
                $personalized_interface = true;
            array_push($quota, $aux);
            $aux = [];
        }

        // Si no existe la quota de correos, la agregamos a la lista aun cuando la aavv no la tenga asignada
        if (!$personalized_mail) {
            $additionalQuotas = $rf->get("AAVVAdditionalQuota")->findOneByArray(['description'=>'email']);

            $aux["id"] = $additionalQuotas->getId();
            $aux["description"] = CoreTranslator::getTransChoice(
                "aavv.additionalQuota.".$additionalQuotas->getDescription(),
                $aavv->getAavvProfile()->count() > 1 ? 1 : 0
            );
            $amount = $additionalQuotas->getAmount();
            $aux['_amount'] = number_format($amount, 0, ',', '.');
            array_push($quota, $aux);
        }

        if (!$personalized_interface) {
            $additionalQuotas = $rf->get("AAVVAdditionalQuota")->findOneByArray(['description'=>'interface']);

            $aux["id"] = $additionalQuotas->getId();
            $aux["description"] = CoreTranslator::getTransChoice(
                "aavv.additionalQuota.".$additionalQuotas->getDescription(),
                $aavv->getAavvProfile()->count() > 1 ? 1 : 0
            );
            $amount = $additionalQuotas->getAmount();
            $aux['_amount'] = number_format($amount, 0, ',', '.');
            array_push($quota, $aux);
        }
        $response['quota'] = $quota;
        $response['personalized_mail'] = $personalized_mail;
        $response['personalized_interface'] = $personalized_interface;
        return $response;
    }

    /**
     * Funcion encargada de agregar las quotas a la agencia de viajes y de la agencia de viajes a la quota
     * @param $aavv
     * @param $rf
     * @param $email_active
     * @param $interface_active
     */
    public function updateQuota($aavv, $rf, $email_active, $interface_active)
    {
        $additionalQuotaRf = $rf->get('AAVVAdditionalQuota');
        $aavvRf = $rf->get('AAVV');

        $additionalQuotas = $additionalQuotaRf->findAll();

        $lengthAdditionalQuota = count($aavv->getAdditionalQuota());
        foreach ($additionalQuotas as $quota) {
            // Si nunca cargo quotas
            if ($lengthAdditionalQuota == 0) {
                switch ($quota->getDescription()) {
                    case 'email':
                        //Si quiere adicionar cargo por correo
                        if ( (!is_null($email_active)) AND $email_active) {
                            $aavv->addAdditionalQuotum($quota);
                            $quota->addAavv($aavv);
                        }
                        break;
                    case 'maintenance':
                    case 'licence':
                        $aavv->addAdditionalQuotum($quota);
                        $quota->addAavv($aavv);
                        break;
                    case 'interface':
                        //Si quiere adicionar cargo por interfaz
                        if ( (!is_null($interface_active)) AND $interface_active) {
                            $aavv->addAdditionalQuotum($quota);
                            $quota->addAavv($aavv);
                        }
                        break;
                }
            }
            else { //Si quiere eliminar la quota adicional por correo
                if (
                    (strcmp($quota->getDescription(), "email") == 0) AND (!is_null($email_active)) AND
                    (!$email_active) AND $aavv->getPersonalizedMail()
                ) {
                    $aavv->removeAdditionalQuotum($quota);
                    $quota->removeAavv($aavv);
                }
                else if (
                    (strcmp($quota->getDescription(), "email") == 0) AND (!is_null($email_active)) AND
                    $email_active AND (!$aavv->getPersonalizedMail() )
                ) {
                    $aavv->addAdditionalQuotum($quota);
                    $quota->addAavv($aavv);
                }

                // Si quiere eliminar la quota adicional por personalizacion de interfaz
                if (
                    (strcmp($quota->getDescription(), "interface") == 0) AND (!is_null($interface_active)) AND
                    (!$interface_active) AND $aavv->getPersonalizedInterface()
                ) {
                    $aavv->removeAdditionalQuotum($quota);
                    $quota->removeAavv($aavv);
                }
                else if (
                    (strcmp($quota->getDescription(), "interface") == 0) AND (!is_null($interface_active)) AND
                    $interface_active AND (!$aavv->getPersonalizedInterface())
                ) {
                    $aavv->addAdditionalQuotum($quota);
                    $quota->addAavv($aavv);
                }
            }
            $additionalQuotaRf->persistObject($quota);
            $aavvRf->persistObject($aavv);
        }
    }
}
