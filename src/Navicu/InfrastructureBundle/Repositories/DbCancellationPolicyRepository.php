<?php 

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Model\Entity\CancellationPolicy;
use Navicu\Core\Domain\Repository\CancellationPolicyRepository;

/**
* La clase se declaran los metodos y funciones que implementan
* el repositorio de la entidad CancellationPolicy
*
* @author Freddy Contreras <freddy.contreras3@gmail.com>
* @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
* @version 21/05/15
*/

class DbCancellationPolicyRepository extends EntityRepository implements 
	CancellationPolicyRepository	
{
	/**
	 * La siguiente función retorna una politica de cancelación
	 * dado el titulo (nombre)
	 *
	 * @author Freddy Contreras <freddycontreras3@gmail.com>
	 * @param string $title
	 * @return CancellationPolicy
	 * @version 11/08/2015
	 */
    public function findByTitle($title)
	{
		return $this->createQueryBuilder('p')
            ->where(
                'type.title = :title'
            )
            ->setParameters(array('title' => $title))
            ->join('p.type','type')
			->getQuery()->getOneOrNullResult();
	}
}