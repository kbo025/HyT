<?php
/**
 * Created by PhpStorm.
 * User: Isabel Nieto <isabelcnd@gmail.com>
 * Date: 09/06/16
 * Time: 10:16 AM
 */
namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Repository\LogsUserRepository;
/**
 * La clase se declaran los metodos y funciones que implementan
 * el repositorio de la entidad LogsUser
 *
 * @author Isabel Nieto <isabelcnd@gmail.com>
 * @author Currently Working: Isabel Nieto <isabelcnd@gmail.com>
 * @version 09/06/16
 */
class DbLogsUserRepository extends EntityRepository implements LogsUserRepository
{
    /**
     * Esta función es usada para retornar desde la BD información del
     * historial por el slug de un establecimiento.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param Integer $slug
     * @return Array
     */
    public function findBySlug($slug)
    {
        $response = $this->createQueryBuilder('l')
            ->join('l.property', 'p')
            ->addOrderBy('l.date', 'DESC')
            ->where('
                p.slug = :slug')
            ->setParameters(array(
                'slug' => $slug
            ) )
            ->getQuery()->getResult();

        return $response;
    }

    /**
     * Esta función es usada para retornar desde la BD información del
     * historial por el nombre del archivo log.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param String $fileName
     * @return Object
     */
    public function findByFileName($fileName)
    {
        return $this->createQueryBuilder('l')
            ->select('l')
            ->where('
                l.file_name = :fileName
                ')
            ->setParameters(array(
                'fileName' => $fileName
            ) )
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * Función para hacer la persistencia en la base de datos.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param Object LogsOwner
     * @return Void
     */
    public function save($obj)
    {
        $this->getEntityManager()->persist($obj);
        $this->getEntityManager()->flush();
    }
}