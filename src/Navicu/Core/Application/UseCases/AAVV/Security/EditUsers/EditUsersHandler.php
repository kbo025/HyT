<?php
namespace Navicu\Core\Application\UseCases\AAVV\Security\EditUsers;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Model\Entity\AAVVProfile;
use Navicu\InfrastructureBundle\Entity\User;

use Navicu\InfrastructureBundle\Entity\Role;
use Navicu\Core\Domain\Adapter\CoreSession;
use Proxies\__CG__\Navicu\Core\Domain\Model\Entity\AAVV;

/**
 * Clase para ejecutar el caso de uso EditRole
 *
 * @author Alejandro Conde <adcs2008@gmail.com>
 * @author Currently Working: Alejandro Conde
 * @version 16/09/2016
 */
class EditUsersHandler implements Handler
{
	public function handle(Command $command, RepositoryFactoryInterface $rf = null)
	{
		$rolerep = $rf->get('Role');

		$user_rep = $rf->get('User');

        $aavv_rep = $rf->get('AAVV');

        $role_rep = $rf->get('Role');

        $locationRep = $rf->get('Location');

		$request = $command->getRequest();

		$users = $command->get('users');

        $personalized_interface = $command->get('personalized_interface');

        $personalized_mail = $command->get('personalized_mail');

        $sessionUser = CoreSession::getUser();

        $userprofile = $sessionUser->getAavvProfile();

        $aavv = $userprofile->getAavv();

        $usersToSave = array();

		try{

           

            if(!empty($users)) {


                	$users_errors = array();

                    $newusers_errors = array();

                    $index = 0;

                    foreach($users as $user) {

                        $user_errors = array();

                        if(!empty($user['id'])) {

                            $userinstance = $user_rep->findOneByArray(['id' => $user['id']]);

                            if ($userinstance != null) {

                                $profile = $userinstance->getAavvProfile();


                                if (!empty($user['fullName']))
                                    $profile->setFullname($user['fullName']);

                                if (!empty($user['email'])) {
                                    $profile->setEmail($user['email']);
                                    $userinstance->setEmail($user['email']);
                                    $userinstance->setUsername($user['email']);
                                } else {
                                    $user_errors['email'] = 'aavv.edit.users.form.errors.empty_field';
                                }

                                if (!empty($user['identification']))
                                    $profile->setDocumentId($user['identification']);

                                if (!empty($user['position']))
                                    $profile->setPosition($user['position']);

                                if (!empty($user['city_id'])) {
                                    $location = $locationRep->find($user['city_id']);
                                    if ($location != null)
                                        $profile->setLocation($location);
                                }

                                if (!empty($user['phone']))
                                    $profile->setPhone($user['phone']);

                                if (!empty($user['password']))
                                    $userinstance->setPlainPassword(($user['password']));

                                if(isset($user['cancellation_email_receiver']))
                                    $profile->setCancellationemailreceiver($user['cancellation_email_receiver']);

                                if(isset($user['news_email_receiver']))
                                    $profile->setNewsEmailReceiver($user['news_email_receiver']);

                                if(isset($user['reservation_email_receiver']))
                                    $profile->setConfirmationemailreceiver($user['reservation_email_receiver']);


                                $newroles = $user['roles']; //Roles a ser asignados

                                $roles = $userinstance->getRole(); //Roles Actuales del usuario

                                $currentroles = array(); //Ids de los roles actuales del usuario

                                foreach ($roles as $role) {
                                    $currentroles[] = $role->getId();
                                }

                                //Roles a ser Removidos
                                foreach ($currentroles as $role) {

                                    if (!in_array($role, $newroles)) {
                                        $roleinstance = $rolerep->findById($role);
                                        if($roleinstance->getName() != 'ROLE_AAVV')
                                            $userinstance->removeRole($roleinstance);
                                    }
                                }

                                $roles = $userinstance->getRole(); //Roles Actuales del usuario

                                $currentroles = array(); //Ids de los roles actuales del usuario

                                foreach ($roles as $role) {
                                    $currentroles[] = $role->getId();
                                }


                                foreach ($newroles as $newrole) {
                                    if (!in_array($newrole, $currentroles)) {
                                        $roleinstance = $rolerep->findById($newrole);
                                        $userinstance->addRole($roleinstance);
                                    }

                                }

                                $usersToSave[] = $userinstance;
                                if(!empty($user_errors)) {
                                    $user_errors['index'] = $index;
                                    $newusers_errors[] = $user_errors;
                                }

                            }
                            $index++;
                        } else {

                            $newuser_errors = array();

                            $newuser = new User();
                            $defaultrole = $role_rep->findByName('ROLE_AAVV');
                            $newuser->addRole($defaultrole);
                            $newuser->setEnabled(false);
                            $newuser->setSubdomain($aavv->getSubdomain());

                            $profile = new AAVVProfile();

                            $profile->setAavv($aavv);

                            $newuser->setAavvProfile($profile);

                            if (!empty($user['email'])) {
                                $profile->setEmail($user['email']);
                                $newuser->setEmail($user['email']);
                                $newuser->setUsername($user['email']);
                            } else {
                                $newuser_errors['email'] = 'aavv.edit.users.form.errors.empty_field';
                            }

                            if (!empty($user['password']) and !empty($user['confirmPassword'])) {
                                if ($user['password'] == $user['confirmPassword']) {
                                    $newuser->setPlainPassword(($user['password']));
                                } else {
                                    $newuser_errors['confirmPassword'] = 'aavv.edit.users.form.errors.invalid_password_confirmation';
                                }
                            } else {
                                if(empty($user['password']))
                                    $newuser_errors['password'] = 'aavv.edit.users.form.errors.empty_field';
                            }

                            if (!empty($user['fullName']))
                                $profile->setFullname($user['fullName']);

                            if (!empty($user['identification']))
                                $profile->setDocumentId($user['identification']);

                            if (!empty($user['position']))
                                $profile->setPosition($user['position']);

                            if (!empty($user['city_id'])) {
                                $location = $locationRep->find($user['city_id']);
                                if ($location != null)
                                    $profile->setLocation($location);
                            }

                            if (!empty($user['phone']))
                                $profile->setPhone($user['phone']);


                            if(isset($user['cancellation_email_receiver']))
                                $profile->setCancellationemailreceiver($user['cancellation_email_receiver']);

                            if(isset($user['news_email_receiver']))
                                $profile->setNewsEmailReceiver($user['news_email_receiver']);

                            if(isset($user['reservation_email_receiver']))
                                $profile->setConfirmationemailreceiver($user['reservation_email_receiver']);

                            $newroles = $user['roles']; //Roles a ser asignados

                            $roles = $newuser->getRole(); //Roles Actuales del usuario

                            $currentroles = array(); //Ids de los roles actuales del usuario

                            foreach ($roles as $role) {
                                $currentroles[] = $role->getId();
                            }

                            //Roles a ser Removidos
                            foreach ($currentroles as $role) {

                                if (!in_array($role, $newroles)) {
                                    $roleinstance = $rolerep->findById($role);
                                    if($roleinstance->getName() != 'ROLE_AAVV')
                                        $newuser->removeRole($roleinstance);
                                }
                            }

                            $roles = $newuser->getRole(); //Roles Actuales del usuario

                            $currentroles = array(); //Ids de los roles actuales del usuario

                            foreach ($roles as $role) {
                                $currentroles[] = $role->getId();
                            }


                            foreach ($newroles as $newrole) {
                                if (!in_array($newrole, $currentroles)) {
                                    $roleinstance = $rolerep->findById($newrole);
                                    $newuser->addRole($roleinstance);
                                }

                            }

                            $usersToSave[] = $newuser;
                            if(!empty($newuser_errors)) {
                                $newuser_errors['index'] = $index;
                                $newusers_errors[] = $newuser_errors;
                            }
                            $index++;
                        }


                    }

                    $newusers_errors = array_filter($newusers_errors);
                    $users_errors = array_filter($users_errors);

                    if(empty($newusers_errors) and empty($users_errors)) {

                        
                            $aavv->setPersonalizedInterface($personalized_interface);


                            $aavv->setPersonalizedMail($personalized_mail);

                        $aavv_rep->save($aavv);

                        foreach ($usersToSave as $currentUser) {

                            $user_rep->save($currentUser);
                        }

                        return new ResponseCommandBus(201, 'OK');
                    } else {
                        return new ResponseCommandBus(400, 'Bad Request', $newusers_errors);
                    }
            }


        } catch(\Exception $e) {
            return new ResponseCommandBus(400,$e->getMessage() . $e->getFile() . $e->getLine());
        }
	}

}