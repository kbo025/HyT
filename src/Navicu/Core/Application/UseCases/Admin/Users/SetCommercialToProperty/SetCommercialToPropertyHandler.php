<?php 
namespace Navicu\Core\Application\UseCases\Admin\Users\SetCommercialToProperty;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Model\Entity\Reservation;


/**
 * SetCommercialToPopertyHandler
 *
 * Handler de Caso de uso Asignar un establecimiento temporal o afiliado
 * a un usuario ROLE_COMMERCIAL
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 */
class SetCommercialToPropertyHandler implements Handler
{
    /**
     *   Instancia del repositoryFactory
     *   @var RepositoryFactory $rf
     */
    protected $rf;

    /**
     * @var
     */
    protected $managerBD;

    /**
     * @param $managerBD
     */
    public function setManagerBD($managerBD)
    {
        $this->managerBD = $managerBD;
    }

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
	    $this->rf = $rf;
        $request = $command->getRequest();

        if ($request['propertyType'] == 1)
            $response = $this->setProperty($request);
        else
            $response = $this->setTempOwner($request);

        if ($response)
            return new ResponseCommandBus(200, 'Ok');
        else
            return new ResponseCommandBus(404, 'Bad Request');
    }

    /**
     * Asignación de un establecimiento temporal a un comercial
     *
     * @param $request
     * @return bool
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 05/04/2016
     */
    private function setTempOwner($request)
    {
        $rpCommercial = $this->rf->get('NvcProfile');
        $rpTempOwner = $this->rf->get('TempOwner');

        $commercialProfile = $rpCommercial->findOneBy(['id' => $request['commercialId']]);
        $tempOwner = $rpTempOwner->findOneBy(['id' => $request['propertyId']]);

        if ($commercialProfile and $tempOwner) {

            try {
                $tempOwner->setNvcProfile($commercialProfile);
                $commercialProfile->addTempOwner($tempOwner);
                $this->managerBD->persist($commercialProfile);
                $this->managerBD->persist($tempOwner);
                $this->managerBD->save();
            } catch (\Exception $e){
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Asignación de un establecimiento afiliado a un comercial
     *
     * @param $request
     * @return bool
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 05/04/2016
     */
    private function setProperty($request)
    {
        $rpCommercial = $this->rf->get('NvcProfile');
        $rpProperty = $this->rf->get('Property');

        $commercialProfile = $rpCommercial->findOneBy(['id' => $request['commercialId']]);
        $property = $rpProperty->findOneBy(['id' => $request['propertyId']]);

        if ($commercialProfile and $property) {

            try {
                $property->setNvcProfile($commercialProfile);
                $commercialProfile->addProperty($property);
                $this->managerBD->persist($commercialProfile);
                $this->managerBD->persist($property);
                $this->managerBD->save();

            } catch (\Exception $e){
                return false;
            }

            return true;
        }

        return false;
    }
}