<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Model\Entity\Document;
use Navicu\Core\Domain\Repository\DocumentRepository;

/**
* La clase se declaran los metodos y funciones que implementan
* el repositorio de la entidad Document
*
* @author Freddy Contreras <freddy.contreras3@gmail.com>
* @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
* @version 21/05/15
*/
class DbDocumentRepository extends DbBaseRepository implements DocumentRepository
{

}