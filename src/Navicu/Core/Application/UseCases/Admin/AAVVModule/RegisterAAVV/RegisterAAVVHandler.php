<?php

namespace Navicu\Core\Application\UseCases\Admin\AAVVModule\RegisterAAVV;


use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Services\AAVVAccountingService;
use Navicu\Core\Application\UseCases\AAVV\Register\Step4\ValidateRegistration\ValidateRegistrationHandler;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Model\Entity\AAVV;
use Navicu\Core\Domain\Model\ValueObject\Slug;
use Navicu\InfrastructureBundle\Entity\Subdomain;
use Navicu\Core\Application\Contract\EmailInterface;


/**
 *  Caso de uso para dar de alta un AAVV
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @version 14-10-2016
 */
class RegisterAAVVHandler implements Handler
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
        $rp = $rf->get('AAVV');
        $aavv = $rp->findOneByArray(['slug' => $command->get('slug')]);

        if ($aavv) {

            if ($aavv->getStatusAgency() == AAVV::STATUS_COMPLETED_REGISTRATION || $aavv->getStatusAgency() == AAVV::STATUS_REGISTRATION_PROCESS) {

                CoreSession::setFinishRegistrationAAVV();
                $validationResponse = ValidateRegistrationHandler::getValidations($aavv,$rf)->getData();
                if(count($validationResponse) > 0 )
                    return new ResponseCommandBus(400,'Bad Request',['message' => 'incomplete_data']);

                $arrayPersist = [];
                $aavv->setRegistrationDate(new \DateTime('now'));
                $aavv->setStatusAgency(AAVV::STATUS_REGISTERED);
                $aavv->setSlug(Slug::generateSlug($aavv->getCommercialName()));
                $profiles = $aavv->getAavvProfile();

                // Creación de subdominio
                $subdomain = new Subdomain();
                $subdomain->setSlug(str_replace('.','',$aavv->getSlug()));
                $subdomain->setType(0);
                $aavv->setSubdomain($subdomain);

                new AAVVAccountingService($rf);
                AAVVAccountingService::setMovement(
                    ['description' => 'initial_credit', 'sign' => '-', 'amount' => $aavv->getCreditInitial()],$aavv
                );
                AAVVAccountingService::balanceCalculator($aavv);
                foreach ($profiles as $profile) {
                    $user = $profile->getUser();
                    $user->setEnabled(true);
                    $user->setSubdomain($subdomain);
                    $subdomain->addUser($user);
                    $profile->setStatus(1);
                    $profile->setLastActivation(new \DateTime('now'));
                    $arrayPersist[] = $profile;
                    $arrayPersist[] = $user;
                }
                $aavv->setHaveCreditZero(
                    $aavv->getCreditAvailable() <= 0
                );

                $arrayPersist[] = $aavv;
                if ($rp->save($arrayPersist)) {
                    $this->processDataEmail($aavv);
                    return new ResponseCommandBus(201, 'ok');
                }
            }

            return new ResponseCommandBus(400,'Bad Request',['message'=>'registered_user']);
        }

        return new ResponseCommandBus(404,'Not Found');
    }

    /**
     * La siguiente función se encarga de enviar el correo a la aavv al dar de alta
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 15/11/2016
     * @param Array $data
     */
    private function processDataEmail($aavv)
    {
        $dataEmail['name'] = $aavv->getCommercialName();
        $dataEmail['public_id'] = $aavv->getPublicId();
        $dataEmail['subDomain'] = $aavv->getSubdomain()->getSlug();

        //Enviamos correo al representante de las aavv
        $dataEmail['email'] = "mmarchan@navicu.com";

        $profiles = $aavv->getAavvProfile();
        foreach ($profiles as $profile) {
            $user = $profile->getUser();
            if ($user->hasRole('ADMIN')) {
                $dataEmail['email'] = $user->getEmail();                
                $dataEmail['username'] = $user->getUsername();                
            }
        }
        $emailService = $this->getEmailService();
        $emailService->setConfigEmail('first_mailer');
        $emailService->setRecipients([$dataEmail['email']]);
        $emailService->setViewParameters($dataEmail);
        $emailService->setViewRender('NavicuInfrastructureBundle:Email:registrationAAVV.html.twig');
        $emailService->setSubject('Ya formas parte de - navicu.com');
        $emailService->sendEmail();
    }

}