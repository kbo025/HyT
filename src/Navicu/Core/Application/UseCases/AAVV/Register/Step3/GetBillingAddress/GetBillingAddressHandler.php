<?php
/**
 * Created by PhpStorm.
 * User: user03
 * Date: 08/09/16
 * Time: 08:41 AM
 */

namespace Navicu\Core\Application\UseCases\AAVV\Register\Step3\GetBillingAddress;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\UseCases\AAVV\Register\Step4\ValidateRegistration\ValidateRegistrationHandler;
use Navicu\Core\Domain\Adapter\CoreTranslator;
use Navicu\Core\Domain\Adapter\EntityValidationException;


class GetBillingAddressHandler implements Handler
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
        try {
            $request = $command->getRequest();
            $response = $this->getBillingAddress($request, $rf);

            return $response;
        } catch (EntityValidationException $e) {
            return new ResponseCommandBus(500, $e->getMessage()."\n".$e->getLine());
        }
    }

    /**
     * Funcion encargada de buscar la direccion de cobro de la agencia de viajes
     *
     * @param $request
     * @param $rf
     *
     * @version 13/10/2016
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @return array, Arreglo con los datos de cobro de la aavv
     */
    public function getBillingAddress($request, $rf)
    {
        $response = [];
        $aavvRf = $rf->get("AAVV");

        $aavv = $aavvRf->findOneByArray(["slug"=>$request['aavv']]);
        $aavvAddresses = $aavv->getAavvAddress();

        foreach ($aavvAddresses as $address) {
            if ($address->getTypeAddress() == 2)
                $newAddress = $address;
        }

        if (!empty($aavv->getSocialReason()))
            $billingAddress['social_reason'] = $aavv->getSocialReason();
        if (!empty($aavv->getRif()))
            $billingAddress['rif'] = $aavv->getRif();

        if ( (count($aavvAddresses) > 0) AND (isset($newAddress)) ) {
            $billingAddress['email'] = $newAddress->getEmail();
            $billingAddress['bank_account'] = $newAddress->getBankAccount();
            $billingAddress['phone'] = $newAddress->getPhone();
            //$billingAddress['swift'] = $newAddress->getSwift();
            $billingAddress['address'] = $newAddress->getAddress();
            $parentLocation = $location = $newAddress->getLocation();
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
                        $billingAddress['country'] = $location->getId(); // Pais
                        break;
                    case 2: // Estado y Pais
                        $billingAddress['state'] = $location->getId(); // Estado
                        $billingAddress['country'] = $location->getParent()->getId(); // Pais
                        break;
                    case 3:
                        $billingAddress['municipality'] = $location->getId(); // Municipio
                        $billingAddress['state'] = $location->getParent()->getId(); //Estado
                        $billingAddress['country'] = $location->getParent()->getParent()->getId(); // Pais
                        break;
                    case 4:
                        $billingAddress['parish'] = $location->getId(); // Parroquia
                        $billingAddress['municipality'] = $location->getParent()->getId(); // Municipio
                        $billingAddress['state'] = $location->getParent()->getParent()->getId(); //Estado
                        $billingAddress['country'] = $location->getParent()->getParent()->getParent()->getId(); // Pais
                        break;
                }
            }
            $billingAddress['zip_code'] = $newAddress->getZipCode();
        }

        // Obtenemos el listado de quotas
        $quota = $this->getQuota($aavv, $rf);

        $personalized = $this->hasPersonalizedMailAndInterface($aavv);

        $response["amount"] = $quota['amount'];
        $response['billing_address'] = isset($billingAddress) ? $billingAddress : [];
        $response['personalized_mail'] = $personalized['personalized_email'];
        $response['personalized_interface'] = $personalized['personalized_interface'];

        $validationResponse = ValidateRegistrationHandler::getValidations($aavv, $rf,3);
        return new ResponseCommandBus(200, 'ok', [
            "data" => $response,
            "validations" => $validationResponse->getData()
        ]);
    }

    /**
     * Funcion encargada de devolver el listado de quotas existentes en la base de datos
     *
     * @param $aavv
     * @param $rf
     * @return array
     * @version 13/10/2016
     * @author Joel Requena
     */
    public function getQuota($aavv, $rf)
    {
        // Si no existen Quoatas por la aavv
        if (count($aavv->getAdditionalQuota()) == 0) {
            $additionalQuotas = $rf->get("AAVVAdditionalQuota")->findAll();
            foreach ($additionalQuotas as $Quota) {
                if ($Quota->getDescription() == "licence") {
                    $totalLicences = ($aavv->getAavvProfile()->count() - 1);
                    $amount = $Quota->getAmount() * $totalLicences;
                    $name[$Quota->getDescription().'_amount'] = number_format($amount,0,',','.');
                    $name[$Quota->getDescription().'_number'] = ($aavv->getAavvProfile()->count() == 0)
                        ? 1
                        : $aavv->getAavvProfile()->count();
                } else {
                    $amount = $Quota->getAmount();
                    $name[$Quota->getDescription() . '_amount'] = number_format($amount, 0, ',', '.');
                }
            }
        } else { // Si ya existen qoutas en la aavv
            $additionalQuotas = $aavv->getAdditionalQuota();
            $foundMail = false;
            $foundInterface = false;
            foreach ($additionalQuotas as $Quota) {
                if ($Quota->getDescription() == "licence") {
                    $totalLicences = ($aavv->getAavvProfile()->count() - 1);
                    $amount = $Quota->getAmount() * $totalLicences;
                    $name[$Quota->getDescription().'_amount'] = number_format($amount,0,',','.');
                    $name[$Quota->getDescription().'_number'] = ($aavv->getAavvProfile()->count() == 0)
                        ? 1
                        : $aavv->getAavvProfile()->count();
                } else {
                    $amount = $Quota->getAmount();
                    $name[$Quota->getDescription() . '_amount'] = number_format($amount, 0, ',', '.');
                }
                if (strcmp($Quota->getDescription(),'email') == 0)
                    $foundMail = true;
                if (strcmp($Quota->getDescription(),'interface') == 0)
                    $foundInterface = true;
            }
            // Si no existio en ningun momento la quota del email devolvemos su valor maestro
            if (!$foundMail) {
                $additionalQuotas = $rf->get("AAVVAdditionalQuota")->findOneByArray(['description'=>'email']);
                $amount = $additionalQuotas->getAmount();
                $name[$additionalQuotas->getDescription() . '_amount'] = number_format($amount, 0, ',', '.');
            }
            if (!$foundInterface) {
                $additionalQuotas = $rf->get("AAVVAdditionalQuota")->findOneByArray(['description'=>'interface']);
                $amount = $additionalQuotas->getAmount();
                $name[$additionalQuotas->getDescription() . '_amount'] = number_format($amount, 0, ',', '.');
            }
        }
        $response['amount'] = $name;
        return $response;
    }

    /**
     * Funcion encargada de buscar en la relacion de aavv con las quotas adicionales
     * si existe la quota "email" y la "interfaz"
     *
     * @param $aavv
     * @return bool
     * @version 13/10/2016
     * @author Isabel Nieto <isabelcnd@gmail.com>
     */
    public function hasPersonalizedMailAndInterface($aavv)
    {
        $personalized['personalized_email'] = true;
        $personalized['personalized_interface'] = true;
        $email = false;
        $interface = false;

        $additionalQuotas = $aavv->getAdditionalQuota();

        // Si no tiene ninguna quota agregada devolvemos true
        if (count($additionalQuotas) == 0) {
            return $personalized;
        }
        foreach ($additionalQuotas as $Quota) {
            if (strcmp($Quota->getDescription(),"email") == 0)
                $email = true;
            if (strcmp($Quota->getDescription(),"interface") == 0)
                $interface = true;

        }
        $personalized['personalized_email'] = $email;
        $personalized['personalized_interface'] = $interface;

        return $personalized;
    }
}
