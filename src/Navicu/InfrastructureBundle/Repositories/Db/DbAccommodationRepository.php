<?php
namespace Navicu\InfrastructureBundle\Repositories\Db;

use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Repository\AccommodationRepository;

/**
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho
 * @version 09-06-2015
 */
class DbAccommodationRepository implements AccommodationRepository
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var string
     */
    private $entityClass;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->entityClass = 'NavicuDomain:Accommodation';
    }

    public function getAccommodationList()
    {
        $res = [];
        $all = $this->em
            ->getRepository($this->entityClass)
            ->findBy([], ['title' => 'ASC']);

        foreach ($all as $accommodation) {
            array_push($res, [
                'id' => $accommodation->getId(),
                'name' => $accommodation->getTitle()
            ]);
        }

        return $res;
    }

    /**
     *	Metodo que devuelve un array Clave Valor en donde la clave es el id
     *  del registro y el valor es el titulo del servicio
     *
     *	@return array
     */
    public function getAllWithKeys()
    {
        $res = [];
        $all = $this->em->getRepository($this->entityClass)->findAll();

        foreach ($all as $accommodation) {
            $res[$accommodation->getId()] = $accommodation;
        }

        return $res;
    }

    /**
     * Busca un tipo de alojamiento por su ID
     *
     * @author Juan Pablo Osorio V. <jpov.nsce@gmail.com>
     * @param  int
     * @return Accommodation
     * @version 10/12/2015
     */
    public function getById($id)
    {
        $acc = $this->em
            ->getRepository($this->entityClass)
            ->findOneBy(['id' => $id]);

        return !empty($acc) ? $acc : null;
    }

    /**
     * Busca un tipo de alojamiento por su nombre
     *
     * @author Juan Pablo Osorio V. <jpov.nsce@gmail.com>
     * @param  int
     * @return Accommodation
     * @version 10/12/2015
     */
    public function getByName($name)
    {
        $acc = $this->em
            ->getRepository($this->entityClass)
            ->findOneBy(['title' => $name]);

        return !empty($acc) ? $acc : null;
    }
}

/* End of file */