<?php
namespace Navicu\Core\Application\UseCases\Admin\ChangeStatusProperty;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Model\Entity\OwnerProfile;


class ChangeStatusPropertyHandler implements Handler
{
    /**
    *   instancia del repositoryFactory
    *   @var RepositoryFactory $rf
    */
    protected $rf;

    /**
     * Ejecuta las tareas solicitadas 
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @author Currently Working: Gabriel Camacho <kbo025@gmail.com>
     * @version 06-08-15
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle( Command $command, RepositoryFactoryInterface $rf )
    {
        $this->rf = $rf;
        //obtengo los repositorios de TempOwner, Category y Location del repositoryFactory
        $property_repository = $rf->get('Property');

        $property = $property_repository->findOneByArray(array('public_id' => $command->get('id')));
        if (isset($property)) {
            $property->setActive($command->get('status'));
	        if($command->get('status') == false) {
	        	$property->setUnpublishedDate(new \DateTime('now'));
	        }
            $property_repository->save($property);
            $response = new ResponseCommandBus(201,'OK');
        } else {
            $response = new ResponseCommandBus(400,'Bad Request',array('message'=>'Property no existe'));
        }
        return $response;        
    }
}