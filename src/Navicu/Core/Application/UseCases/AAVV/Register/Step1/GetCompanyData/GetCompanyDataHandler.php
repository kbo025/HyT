<?php
namespace Navicu\Core\Application\UseCases\AAVV\Register\Step1\GetCompanyData;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\InfrastructureBundle\Entity\User;
use Navicu\InfrastructureBundle\Entity\Role;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Model\ValueObject\Coordinate;
use Navicu\Core\Domain\Model\Entity\AAVV;
use Navicu\Core\Domain\Model\Entity\AAVVProfile;
use Navicu\Core\Domain\Model\Entity\AAVVAddress;
use Navicu\Core\Application\UseCases\AAVV\Register\Step4\ValidateRegistration\ValidateRegistrationHandler;
use Navicu\Core\Domain\Adapter\CoreSession;
/**
 * Clase para ejecutar el caso de uso GetCompanyData
 *
 * @author Alejandro Conde <adcs2008@gmail.com>
 * @author Currently Working: Alejandro Conde
 * @version 06/09/2016
 */
class GetCompanyDataHandler implements Handler
{
    public function handle(Command $command, RepositoryFactoryInterface $rf = null)
    {
        $request = $command->getRequest();

        $response = array();

        $form = array();


        $files = array();

        $users = array();

        $locationRep = $rf->get('Location');
        //Obtener la estructura de paises estados y ciudades
        $locations = $locationRep->getAll();

        $user_rep = $rf->get('User');

        $aavv_rep = $rf->get('AAVV');

        try {



            $user = CoreSession::getUser();

            if ($user) {

                $slug = CoreSession::get('sessionAavv');

                if(is_null($slug)) {

                    $aavvProfile = $user->getAavvProfile();

                    $aavv = $aavvProfile->getAavv();
                } else{
                    $aavv = $aavv_rep->findOneByArray(['slug' => $slug]);
                }
                $validationResponse = ValidateRegistrationHandler::getValidations($aavv, $rf, 1);


                if (!$aavv->isEmpty()) {

                    $form['company_name'] = $aavv->getSocialReason();
                    $form['commercial_name'] = $aavv->getCommercialName();
                    $form['opening_year'] = $aavv->getOpeningYear();
                    $form['rif'] = $aavv->getRif();
                    $form['company_email'] = $aavv->getCompanyEmail();
                    $form['phone'] = $aavv->getPhone();
                    //NAVICU-3572
                    //$form['merchant_id'] = $aavv->getMerchantId();
                    //$form['status'] = $aavv->getStatus();

                    $coordinates = $aavv->getCoordinates();
                    if ($coordinates != null) {

                        $form['latitude'] = $coordinates['latitude'];
                        $form['longitude'] = $coordinates['longitude'];
                    } else {
                        $form['latitude'] = 10.23436013922387;
                        $form['longitude'] = -68.0059865878967;
                    }

                    $addresses = $aavv->getAavvAddress();
                    foreach ($addresses as $address) {
                        if ($address->getTypeAddress() == 0) {
                            $mainAddress = $address;
                        }
                    }
                    if (isset($mainAddress)) {
                        $location = $mainAddress->getLocation();
                        $form['location'] = $location->getId();

                        if ($location->getLvl() == 3) {
                            $form['parish'] = $location
                                ->getId();

                            $form['city'] = $location
                                ->getParent()
                                ->getId();

                            $form['state'] = $location
                                ->getParent()
                                ->getParent()
                                ->getId();

                            $form['country'] = $location
                                ->getParent()
                                ->getParent()
                                ->getParent()
                                ->getId();

                        }
                        if ($location->getLvl() == 2) {
                            $form['parish'] = null;
                            $form['city'] = $location
                                ->getId();

                            $form['state'] = $location
                                ->getParent()
                                ->getId();

                            $form['country'] = $location
                                ->getParent()
                                ->getParent()
                                ->getId();

                        }
                        if ($location->getLvl() == 1) {
                            $form['parish'] = null;
                            $form['city'] = null;
                            $form['state'] = $location
                                ->getId();

                            $form['country'] = $location
                                ->getParent()
                                ->getId();

                        }
                        if ($location->getLvl() == 0) {
                            $form['parish'] = null;
                            $form['city'] = null;
                            $form['state'] = null;
                            $form['country'] = $location
                                ->getId();

                        }

                        $form['zip'] = $mainAddress->getZipCode();
                        $form['address'] = $mainAddress->getAddress();

                    } else {

                        $form['parish'] = null;

                        $form['city'] = null;

                        $form['state'] = null;

                        $form['country'] = null;
                        $form['zip'] = null;
                        $form['address'] = null;
                    }


                    $documents = $aavv->getDocuments();
                    $images = null;
                    if ($documents->count() == 0) {
                        $files['tourism_document'] = null;
                        $files['rif_document'] = null;
                        $files['lease_document'] = null;
                        $form['logo'] = null;
                        $form['images'] = null;
                    } else {
                        foreach ($documents as $document) {
                            if ($document->getType() == 'LOGO') {
                                $form['logo'] = $document->getDocument()->getFileName();
                            } elseif ($document->getType() == 'LEASE') {
                                $files['lease_document'] = $document->getDocument()->getFileName();
                                $form['lease_document'] = $document->getDocument()->getName();
                            } elseif ($document->getType() == 'RIF') {
                                $files['rif_document'] = $document->getDocument()->getFileName();
                                $form['rif_document'] = $document->getDocument()->getName();
                            } elseif ($document->getType() == 'RTN') {
                                $files['tourism_document'] = $document->getDocument()->getFileName();
                                $form['tourism_document'] = $document->getDocument()->getName();
                            } else {
                                $images[] = $document->getDocument()->getFileName();
                            }
                        }
                        $form['images'] = $images;

                        if (!isset($form['logo']))
                            $form['logo'] = null;
                        if (!isset($files['lease_document']))
                            $files['lease_document'] = null;
                        if (!isset($files['tourism_document']))
                            $files['tourism_document'] = null;
                        if (!isset($files['rif_document']))
                            $files['rif_document'] = null;
                    }


                } else {
                    $form = null;
                }

                $profiles = $aavv->getAavvProfile();


                foreach ($profiles as $currentUser) {

                    $user = array();

                    $user['id'] = $currentUser->getUser()->getId();
                    $user['order'] = $currentUser->getProfileOrder();
                    $user['name'] = $currentUser->getFullname();
                    $user['identity_card'] = $currentUser->getDocumentId();
                    $user['email'] = $currentUser->getUser()->getEmail();
                    $user['phone'] = $currentUser->getPhone();

                    $currentLocation = $currentUser->getLocation();
                    if ($currentLocation != null) {
                        $user['country'] = $currentLocation->getParent()->getParent()->getId();
                        $user['state'] = $currentLocation->getParent()->getId();

                        $user['city'] = $currentLocation->getId();
                    } else {
                        $user['country'] = null;
                        $user['state'] = null;
                        $user['city'] = null;
                    }
                    $user['confirmation_email_receiver'] = $currentUser->getConfirmationemailreceiver();
                    $user['cancellation_email_receiver'] = $currentUser->getCancellationemailreceiver();
                    $user['news_email_receiver'] = $currentUser->getNewsEmailReceiver();

                    $users[] = $user;


                }

                usort($users, function ($a, $b) {
                    return $a['order'] - $b['order'];
                });

                $validations_array = $validationResponse->getData();

                $validations_array = array_filter($validations_array);

                if (empty($validations_array))
                    $validations = false;
                else
                    $validations = true;

                $validations_array = $validationResponse->getData();

                $validations_array = array_filter($validations_array);

                if (empty($validations_array))
                    $validations = false;
                else
                    $validations = true;

                $response['locations'] = $locations;
                $response['form'] = $form;
                $response['users'] = $users;
                $response['files'] = $files;
                $response['validation'] = $validations;


                return new ResponseCommandBus(200, 'OK', $response);

            } else {

                return new ResponseCommandBus(401, 'No autorizado');
            }


        } catch (\Exception $e) {
            return new ResponseCommandBus(400, $e->getMessage());
        }

    }
}

