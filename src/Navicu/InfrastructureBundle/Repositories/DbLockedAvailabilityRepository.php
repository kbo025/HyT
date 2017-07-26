<?php
namespace Navicu\InfrastructureBundle\Repositories;

use Navicu\Core\Domain\Repository\LockedAvailabilityRepository;


/**
 * repositorio de la entidad LockedAvailability que se encarga del bloqueo de la disponibilidad
 */
class DbLockedAvailabilityRepository extends DbBaseRepository implements LockedAvailabilityRepository
{

    /**
     * elimina masivamente un conjunto de bloqueos dado un conjunto de id de session
     *
     * @param $session
     * @internal param $sessions
     * @return integer
     */
    public function deleteBySessions($session)
    {
        if(is_array($session)) {
            $stringSessions = '';
            foreach ($session as $current) {
                if(is_string($current))
                    $stringSessions = $stringSessions."'".$current."', ";
            }
            $sql = "DELETE NavicuDomain:LockedAvailability u WHERE u.id_session IN (" . trim(trim($stringSessions),',') . ")";
        } else
            $sql = "DELETE NavicuDomain:LockedAvailability u WHERE u.id_session='".$session."'";
        $query = $this->_em->createQuery($sql);
        $numResult = $query->execute();
        $this->clear();
        return $numResult;
    }

    /**
     * elimina masivamente el conjunto de bloqueos expirados
     *
     * @return integer
     */
    public function cleanExpired()
    {
        $now = strtotime("now");
        $query = $this->_em->createQuery('DELETE NavicuDomain:LockedAvailability u WHERE u.expiry <= '.$now);
        $numResult = $query->execute();
        $this->clear();
        return $numResult;
    }

    /**
     * renueva el tiempo de expiración de los bloqueos que corresponden a un id Session
     */
    public function renewSessionReservation($idSessions,$time = 600)
    {
        $lockeds = [];
        foreach($idSessions as $idSession) {
            $lockeds = array_merge($lockeds, $this->findBy(['id_session' => $idSession]));
        }
        $newData = [];
        //if (!empty($lockeds)) {
            foreach ($lockeds as $lock) {
                if ($lock) {
                    $newTime = $lock->getExpiry() + $time;
                    $lock->setExpiry($newTime);
                    $newData[] = $lock;
                }
            }
            $this->save($newData);
            return true;
        //}
        //return false;
    }
}