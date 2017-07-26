<?php
namespace Navicu\Core\Application\UseCases\AAVV\Security\GetUsers;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\InfrastructureBundle\Entity\User;
use Navicu\InfrastructureBundle\Entity\Role;

use Navicu\Core\Domain\Adapter\CoreSession;

class GetUsersHandler implements Handler
{
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {   

        $response = array();

        $form = array();

        $users = array();

        $data = array();

        $user_rep = $rf->get('User');

        $aavv_rep = $rf->get('AAVV'); 

        try{

            $user = CoreSession::getUser();

            if($user) {

                $aavvProfile = $user->getAavvProfile();

                $aavv = $aavvProfile->getAavv();

                $profiles = $aavv->getAavvProfile();




                    foreach($profiles as $currentUser) {

                        if($currentUser->getStatus() != 3) {
                            $user = array();
                            $roles = array();

                            $user['id'] = $currentUser->getUser()->getId();
                            $user['status'] = $currentUser->getStatus();

                            $user['fullName'] = $currentUser->getFullname();
                            $user['identification'] = $currentUser->getDocumentId();
                            $user['email'] = $currentUser->getUser()->getEmail();
                            $user['position'] = $currentUser->getPosition();


                            $currentLocation = $currentUser->getLocation();
                            if ($currentLocation != null) {

                                $user['city_id'] = $currentLocation->getId();
                                $user['state_id'] = $currentLocation->getParent()->getId();
                                $user['country_id'] = $currentLocation->getParent()->getParent()->getId();
                            } else {
                                $user['city_id'] = null;
                                $user['state_id'] = null;
                                $user['country_id'] = null;
                            }
                            $user['phone'] = $currentUser->getPhone();

                            $user['reservation_email_receiver'] = $currentUser->getConfirmationemailreceiver();
                            $user['cancellation_email_receiver'] = $currentUser->getCancellationemailreceiver();
                            $user['news_email_receiver'] = $currentUser->getNewsEmailReceiver();

                            $currentRoles = $currentUser->getUser()->getRole();

                            foreach ($currentRoles as $role) {

                                $roles[] = $role->getId();
                            }

                            $user['roles'] = $roles;

                            $users[] = $user;

                        }
                    }

                    $data['users'] = $users;
                    $data['personalized_interface'] = $aavv->getPersonalizedMail();
                    $data['personalized_mail'] = $aavv->getPersonalizedInterface();

                    return new ResponseCommandBus(201,'ok', $data);
            }


        } catch(\Exception $e) {
            return new ResponseCommandBus(400,$e->getMessage());
        }
    }
}