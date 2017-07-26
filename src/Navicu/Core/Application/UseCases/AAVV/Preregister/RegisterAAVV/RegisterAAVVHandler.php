<?php
namespace Navicu\Core\Application\UseCases\AAVV\Preregister\RegisterAAVV;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\InfrastructureBundle\Entity\User;
use Navicu\InfrastructureBundle\Entity\Role;
use Navicu\Core\Application\Contract\EmailInterface;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Model\Entity\AAVVProfile;

/**
 * Clase para ejecutar el caso de uso RegisterAAVV
 *
 * @author Alejandro Conde <adcs2008@gmail.com>
 * @author Currently Working: Alejandro Conde
 * @version 19/08/2016
 */
class RegisterAAVVHandler implements Handler
{


	/**
     * La variable contiene la funcionalidades del servicio de email
     *
     * @var EmailInterface $emailService
     */
    protected $emailService;

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
     * Metodo que implementa y ejecuta el conjunto de acciones que completan
     * el caso de uso Registrar Usuario Agencia de Viaje
	 *
     *  @param CreateTempOwnerCommand $command
     *  @param RepositoryFactory $rf
	 */
	public function handle(Command $command, RepositoryFactoryInterface $rf = null)
	{
		try{
            $user_rep = $rf->get('User');
            $role_rep = $rf->get('Role');
    		$request = $command->getRequest();

            if($request['password'] == $request['confirmpassword']) {

    		
        		$email = new EmailAddress($request['email']);
        		$password = $request['password'];

        		$random_hash = substr(md5(uniqid(rand(), true)), 16, 16);

        		
        		$user = new User();

        		$user->setUsername($email->toString());
        		$user->setEmail($email->toString());
        		$user->setPlainPassword($password);

                $role = $role_rep->findByName('ROLE_AAVV');

        		$user->addRole($role);
        		$user->setEnabled(false);
        		$user->setConfirmationToken($random_hash);

                $profile = new AAVVProfile();

                $profile->setPhone($request['phone']);
                $profile->setFullname($request['fullname']);


                $user->setAavvProfile($profile);    

        		$user_rep->save($user);

        		$emailService = $this->getEmailService();
                $emailService->setConfigEmail('first_mailer');
                $emailService->setRecipients([$email->toString()]);

                $emailparams['username'] = $user->getUsername();
                $emailparams['token'] = $user->getConfirmationToken();
                $emailService->setViewParameters($emailparams);
                $emailService->setViewRender('NavicuInfrastructureBundle:Email:confirmAAVVRegistration.html.twig');
                $emailService->setSubject('Confirmación de registro en navicu.com - Vicander');
                $emailService->setEmailSender('info@navicu.com');
                $emailService->sendEmail();

        		return new ResponseCommandBus(
                                200,
                                'Usuario Inactivo Registrado',
                                null
                            );
            } else {
                return new ResponseCommandBus(400,'las contraseñas no coinciden');
            }

		} catch(\Exception $e) {
			return new ResponseCommandBus(400,$e->getMessage());
		}

	}

}