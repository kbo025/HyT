<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Navicu\Core\Domain\Repository\BankTypeRepository;

/**
 *	@author Gabriel Camacho <kbo025@gmail.com>
 *	@author Currently Working: Gabriel Camacho
 */
class DbBankTypeRepository extends EntityRepository implements BankTypeRepository
{
    /**
     *  busca un registro por su id
     *
     * @param string $id
     * @return Object
     */
    public function findById($id)
    {
        return $this->find($id);
    }

    public function getListBanksArray($location = 1, $receiver = false)
    {
        $response = [];
        $params = ['location_zone' => $location];
        if ($receiver)
            $params = array_merge($params,['receiver' => $receiver]);
        $all = $this->findBy( $params, ['title' => 'ASC']);
        foreach ($all as $bank)
        {
            $response[] = [
                'id' => $bank->getId(),
                'title' => $bank->getTitle()
            ];
        }
        return $response;
    }
} 