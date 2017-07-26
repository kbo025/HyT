<?php
namespace Navicu\Core\Application\UseCases\Admin\Users\RegisterUser;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Model\Entity\User;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Model\ValueObject\Password;
use Navicu\InfrastructureBundle\Tests\Model\ValueObject\EmailAddressTest;


/**
 * Class RegistUserHandler
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 */
class RegisterUserHandler implements Handler
{
    /**
     *   Instancia del repositoryFactory
     *   @var RepositoryFactory $rf
     */
    protected $rf;

    /**
     * @var array contiene los errores del registro
     */
    public $error = [];

    /**
     * Ejecuta las tareas solicitadas
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     *
     * @return ResponseCommandBus
     */
    public function handle( Command $command, RepositoryFactoryInterface $rf )
    {
        $request = $command->getRequest();
        $this->rf = $rf;

        // Se validan los datos basicos para un usuario
        // Datos comunes para todos los usarios
        try {

            if (!$request['role'])
                $this->error['role'] = '* Rol inválido';

            if (!is_string($request['userName']) or $request['userName'] == '')
                $this->error['userName'] = '* Nombre de usuario';

            try {
                new EmailAddress($request['email']);
            } catch(\Exception $e) {
                $this->error['email'] = $e->getMessage();
            }

            try {
                new Password($request['password']);
            } catch(\Exception $e) {
                $this->error['password'] = $e->getMessage();
            }

            //Se validan los datos necesarios por rol
            try {
                switch ($request['role']) {
                    case 'ROLE_COMMERCIAL':
                        $this->registerCommercial($request);
                        break;
                    case 'ROLE_ADMIN':
                        $this->registerAdmin($request);
                        break;
                    default:
                        $error['role'] = '* Rol invalido';
                        break;
                }
            } catch (\Exception $e) {
                $error['entity'] = 'error entity';
            }

            if (!empty($this->error)) {
                return new ResponseCommandBus(404,null, $this->error);
            }

            return new ResponseCommandBus(200,'Ok');

        } catch(\Exception $e) {
            return new ResponseCommandBus(500,"\n".$e->getMessage()."\n".$e->getFile()."\n".$e->getLine());
        }

        return new ResponseCommandBus(200, 'Ok' );
    }

    /**
     * La siguiente función se encarga de registrar el usuario comercial
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $request
     * @version 04/04/2016
     */
    private function registerCommercial($request)
    {

        if (!$request['fullName'])
            $this->error['fullName'] = '* Nombre inválido';

        if (!filter_var($request['identityCard'], FILTER_VALIDATE_INT))
            $this->error['identityCard'] = '* Cedula inválida';

        if (empty($this->error)) {
            $rpCommercialProfile = $this->rf->get('CommercialProfile');
            $rpCommercialProfile->registerUser($request);
        }
    }

}