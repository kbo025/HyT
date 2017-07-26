<?php 

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Model\Entity\Notification;
use Navicu\Core\Domain\Repository\NotificationRepository;

/**
* La clase se declaran los metodos y funciones que implementan
* el repositorio de la entidad Notification
*
* @author Freddy Contreras <freddy.contreras3@gmail.com>
* @author Currently Working: Gabriel Camacho <kbo025@gmail.com>
* @version 06/10/15
*/

class DbNotificationRepository extends EntityRepository implements NotificationRepository
{
    /**
     * Almacena en BD toda la informacion referente a la
     * Notification.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     * 
     * @param $notification
     */
    public function save(Notification $notification)
    {
        $this->getEntityManager()->persist($notification);
        $this->getEntityManager()->flush();
    }

    /**
     * Esta función es usada para buscar dentro de la BD
     * las notificaciones mas recientes de un usuario.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     * 
     * @param $notification
     */
    public function findByRecient($userId)
    {
        return $this->createQueryBuilder('n')
            ->where('
                n.reciver = :userId and
                n.view = false
                order by n.date
                ')
            ->setParameters(
                array(
                    'userId' => $userId
                )
            )
            ->getQuery()->getResult();
    }
}