<?php
namespace Navicu\Core\Application\UseCases\AAVV\Register\Step1\SetCompanyData;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreUser;
use Navicu\InfrastructureBundle\Entity\User;
use Navicu\InfrastructureBundle\Entity\Role;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Model\ValueObject\Coordinate;
use Navicu\Core\Domain\Model\Entity\AAVV;
use Navicu\Core\Domain\Model\Entity\AAVVProfile;
use Navicu\Core\Domain\Model\Entity\AAVVAddress;

use Navicu\Core\Domain\Adapter\CoreSession;
/**
 * Clase para ejecutar el caso de uso RegisterAAVV
 *
 * @author Alejandro Conde <adcs2008@gmail.com>
 * @author Currently Working: Alejandro Conde
 * @version 06/09/2016
 */
class SetCompanyDataHandler implements Handler
{
	public function handle(Command $command, RepositoryFactoryInterface $rf = null)
	{
		$request = $command->getRequest();

		$user_rep = $rf->get('User');

		$aavv_rep = $rf->get('AAVV');

		$role_rep = $rf->get('Role');

		$location_rep = $rf->get('Location');

		
		$errors = array();
		$company_errors = array();
		$users_errors = array();

		$usersToSave = array();



		try{

			//$user = $user_rep->findByUserNameOrEmail($request['username']);

			$user = CoreSession::getUser();
            $aavvProfile = null;

            if($user instanceof User)
                $aavvProfile = $user->getAavvProfile();

            if(is_null($aavvProfile)) {
                $slug = CoreSession::get('sessionAavv');
                $aavv = $aavv_rep->findOneByArray(['slug' => $slug]);
            } else {
                $aavv = $aavvProfile->getAavv();
            }

			if(!empty($aavv)){

				if (!empty($request['form']['commercial_name']))
					$aavv->setCommercialName($request['form']['commercial_name']);
				else
					$company_errors['commercial_name'] = 'El nombre comercial no debe estar vacio';

				if (!empty($request['form']['company_name']))
	            	$aavv->setSocialReason($request['form']['company_name']);
	            else
	            	$company_errors['social_reason'] = 'La razon social no debe estar vacia';
	            
	            if (!empty($request['form']['rif']))
	            	$aavv->setRif($request['form']['rif']);
	            else
	            	$company_errors['rif'] = 'El rif no debe estar vacio';
	            
	            /* comentado por historia NAVICU-3572
                if (!empty($request['form']['merchant_id']))
	            	$aavv->setMerchantId($request['form']['merchant_id']);
	            else
	            	$company_errors['merchant_id'] = 'El numero de registro mercantil no debe estar vacio';

	            if (!empty($request['form']['status']))
	            	$aavv->setStatus($request['form']['status']);
	            else
	            	$company_errors['status'] = 'El Estado del registro mercantil no debe estar vacio';*/
	            
	            if (!empty($request['form']['company_email']))
	            	$aavv->setCompanyEmail($request['form']['company_email']);
	            else
	            	$company_errors['company_email'] = 'El email no debe estar vacio';
	            		
	            if (!empty($request['form']['phone']))
	            	$aavv->setPhone($request['form']['phone']);
	            else
	            	$company_errors['phone'] = 'El telefono no debe estar vacio';

	            if (!empty($request['form']['opening_year']))
	            	$aavv->setOpeningYear($request['form']['opening_year']);
	            else
					$company_errors['opening_year'] = 'El año de apertura no debe estar vacio';

				if (!empty($request['form']['longitude']) and !empty($request['form']['latitude'])) {
                    try {
                        $aavv->setCoordinates(new Coordinate($request['form']['longitude'],$request['form']['latitude']));
                    } catch( \Exception $e) {
                        $company_errors['ubicacion'] = 'Existe un problema con las coordenadas';
                    }
                } else {
                    $company_errors['ubicacion'] = 'Las Coordenadas no deben estar vacias';
                }

                //if (!empty($request['form']['address']) or !empty($request['form']['zip']))
	            	try{
                        $addresses = $aavv->getAavvAddress();

                        if($addresses->count() > 0) {
                            foreach($addresses as $address) {
                                if($address->getTypeAddress() == 0) {
                                    $aavvAddress = $address;
                                }
                            }

                            if(isset($aavvAddress)) {
                                if (!empty($request['form']['address']))
                                    $aavvAddress->setAddress($request['form']['address']);
                                if (!empty($request['form']['zip']))
                                    $aavvAddress->setZipCode($request['form']['zip']);
                            }

                        } else {
	            		    $aavvAddress = new AAVVAddress(0, $location_rep->find(1)); //Direccion
                            if (!empty($request['form']['address']))
                                $aavvAddress->setAddress($request['form']['address']);
                            if (!empty($request['form']['zip']))
                                $aavvAddress->setZipCode($request['form']['zip']);
                            $aavvAddress->setAavv($aavv);
	            		    $aavv->addAavvAddress($aavvAddress);
                        }


	            	} catch(\Exception $e) {
	            		$company_errors['address'] = 'Existe un problema con la direccion o el codigo postal';
	            	}
	            //else
				//	$company_errors['address'] = 'La direccion y el codigo postal no deben estar vacios';



                if(!empty($aavvAddress)){
                    if (!empty($request['form']['parish'])) {
                        $location = $location_rep->find($request['form']['parish']);
                        if (isset($location)) {
                            $aavvAddress->setLocation($location);
                        } else {
                            $company_errors['location'] = 'Existe un error en la ubicacion';
                        }
                    } elseif (!empty($request['form']['country']) and empty($request['form']['parish'])) {
                        $location = $location_rep->find($request['form']['country']);
                        if (isset($location)) {
                            $aavvAddress->setLocation($location);
                        } else {
                            $company_errors['location'] = 'Existe un error en la ubicacion';
                        }
                    }
                } else {
                   		$company_errors['location']= 'La ubicacion no debe estar vacia';
                }


                //Registro de Usuarios
                if(!empty($request['users'])){

                	$users = $request['users'];
                	//Se instancia cada usuario y se graban solo si todos tienen los datos completos
                	$index = 1;
                	foreach($users as $user){

                	    if (!empty($user['email'])) {

                            $user_errors = array();
                            if (empty($user['id'])) {
                                $currentuser = new User();
                                if (empty($user['password']))
                                    $currentuser->setPlainPassword('temporal');
                                $profile = new AAVVProfile();
                                $profile->setAavv($aavv);
                                $currentuser->setAavvProfile($profile);
                            } else {
                                $currentuser = $user_rep->findOneByArray(['id' => $user['id']]);
                                $profile = $currentuser->getAavvProfile();
                            }
                            if (!empty($user['email'])) {
                                $email = new EmailAddress($user['email']);
                                $currentuser->setUsername($email->toString());
                                $currentuser->setEmail($email->toString());

                                $profile->setEmail($email->toString());
                            } else {
                                $user_errors['email'] = 'El Email no puede estar vacio';
                            }

                            if (!empty($user['password'])){
                                CoreUser::updatePassword($currentuser, $user['password']);
                                }
                            else
                                $user_errors['password'] = 'La contraseña no puede estar vacia';

                            $adminrole = $role_rep->findInAavv('ADMIN', $aavv->getId());
                            $userrole = $role_rep->findInAavv('USER', $aavv->getId());
                            $aavvrole = $role_rep->findByName('ROLE_AAVV');


                            if ($index == 1) {
                                if (!$currentuser->hasRole($adminrole->getName()))
                                    $currentuser->addRole($adminrole);
                            } else {
                                if (!$currentuser->hasRole($userrole->getName())) {
                                    $currentuser->addRole($userrole);
                                    $currentuser->addRole($aavvrole);
                                    $currentuser->setEnabled(false);
                                }
                            }


                            if (!empty($user['name']))
                                $profile->setFullname($user['name']);
                            else
                                $user_errors['name'] = 'El nombre del usuario no puede estar vacio';

                            if (!empty($user['identity_card']))
                                $profile->setDocumentId($user['identity_card']);
                            else
                                $user_errors['identity_card'] = 'La cedula del usuario no puede estar vacio';

                            if (!empty($user['position']))
                                $profile->setPosition($user['position']);
                            else
                                $user_errors['position'] = 'el cargo del usuario no puede estar vacia';

                            if (!empty($user['city'])) {
                                $location = $location_rep->find($user['city']);
                                if (isset($location)) {
                                    $profile->setLocation($location);
                                } else {
                                    $user_errors['location'] = 'Existe un error en la ubicacion del usuario';
                                }
                            } else {
                                $user_errors['location'] = 'La ubicacion no debe estar vacia';
                            }

                            if (!empty($user['phone']))
                                $profile->setPhone($user['phone']);
                            else
                                $user_errors['phone'] = 'El telefono del usuario no puede estar vacio';

                            if (!empty($user['confirmation_email_receiver']))
                                $profile->setConfirmationemailreceiver(true);
                            else
                                $profile->setConfirmationemailreceiver(false);

                            if (!empty($user['cancellation_email_receiver']))
                                $profile->setCancellationemailreceiver(true);
                            else
                                $profile->setCancellationemailreceiver(false);

                            if (!empty($user['news_email_receiver']))
                                $profile->setNewsEmailReceiver(true);
                            else
                                $profile->setNewsEmailReceiver(false);

                            $profile->setProfileOrder($index);

                            $user_errors['index'] = $index;


                            $usersToSave[] = $currentuser;

                            $users_errors[] = $user_errors;

                            $index++;
                        }
                	}


                }
                $company_errors = array_filter($company_errors);
                $users_errors = array_filter($users_errors);
                $errors['company'] = $company_errors;
				$errors['users'] = $users_errors;
			
				
				/*if (empty($company_errors) and empty($users_errors)) {
					$aavv_rep->save($aavv);
					foreach($usersToSave as $currentUser) {
						$user_rep->save($currentUser);
					}


					return new ResponseCommandBus(200,'OK');
				} else {
					$errors['company'] = $company_errors;
					$errors['users'] = $users_errors;
					return new ResponseCommandBus(400,'Bad Request',$errors);
				}*/

				$aavv_rep->save($aavv);
					foreach($usersToSave as $currentUser) {
						$user_rep->save($currentUser);
					}
				return new ResponseCommandBus(200,'OK',$errors);

			} else {
				return new ResponseCommandBus(400,'Bad Request', $errors);
			}

		} catch(\Exception $e) {
			return new ResponseCommandBus(400,$e->getMessage() . $e->getFile() . $e->getLine());
		}
		

	}

	

            
            
            
	

}