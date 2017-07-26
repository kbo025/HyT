<?php

namespace Navicu\Core\Application\UseCases\Admin\Users\Owner\CreateUserOwner;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreUser;
use Navicu\Core\Application\Contract\EmailInterface;
use Navicu\Core\Domain\Model\Entity\OwnerProfile;

/**
 * CreateUserOwner
 *
 * El siguiente caso obtiene datos de un usuario administrador
 * dado un id del usuario de fos_user
 *
 * @author Freddy Contreras
 */
class CreateUserOwnerHandler implements Handler
{
    /**
     * La variable contiene la funcionalidades del servicio de email
     *
     * @var EmailInterface $emailService
     */
    protected $emailService;

    /**
     * Método Get del la interfaz del serivicio Email
     * @internal param EmailInterface $emailService
     * @return \Navicu\Core\Application\Contract\EmailInterface
     */
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
     *  Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $rpNvcProfile = $rf->get('User');
        $request = $command->getRequest();

        $password = false;
        $user = CoreUser::generateUser($request,$password);
        $userOwner = new OwnerProfile();
        $userOwner->setUser($user);
        $user->setOwnerProfile($userOwner);

        $userOwner->setAtributes($request);
        $rpNvcProfile->save($userOwner);

        $this->sendEmail($request, $password);

        return new ResponseCommandBus(201,'Ok');
    }

    /**
     * Envia el correo al usuario luego de ser registrado
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $emailData
     */
    public function sendEmail($emailData, $password)
    {
        $emailData['fullName'] = $emailData['full_name'];
        $emailData['generatedPassword'] = true;
        $emailData['password'] = $password;

        $emailService = $this->getEmailService();
        $emailService->setConfigEmail('first_mailer');

        $emailService->setRecipients([$emailData['email']]);
        $emailService->setViewParameters($emailData);
        $emailService->setViewRender('NavicuInfrastructureBundle:Email:singInClient.html.twig');
        $emailService->setSubject('Bienvenido a navicu.com - Datos del registro');
        $emailService->setEmailSender('info@navicu.com');
        $emailService->sendEmail();
    }
}