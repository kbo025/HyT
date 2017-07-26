<?php


namespace Navicu\InfrastructureBundle\EventListener;

use Doctrine\ORM\Events;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\Common\EventSubscriber;
use Navicu\Core\Domain\Model\Entity\AAVV;
use Navicu\Core\Domain\Model\Entity\AAVVAddress;
use Navicu\Core\Domain\Model\Entity\AAVVLog;
use Navicu\Core\Domain\Model\Entity\AAVVProfile;
use Navicu\InfrastructureBundle\Entity\User;


class CallbackSubscriber implements EventSubscriber
{

    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }
    public function getSubscribedEvents()
    {
        return array(
            'onFlush',
        );
    }

    public function onFlush(OnFlushEventArgs  $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $token = $this->container->get('security.context')->getToken();

        if(!is_null($token))
            $user = $token->getUser();
        else
            $user = null;

        if($user instanceof User) {
            $userId = $user->getId();

            //INSERTS
            foreach ($uow->getScheduledEntityInsertions() as $keyEntity => $entity) {
                if ($entity instanceof AAVVProfile) {
                    foreach ($uow->getEntityChangeSet($entity) as $keyField => $field) {
                        if(!in_array($keyField, ['aavv', 'id', 'profileorder'])) {
                            $log = new AAVVLog();
                            $log->setType('CREATE');
                            $log->setDate(new \DateTime('now'));
                            $log->setAavvId($entity->getAavv() ? $entity->getAavv()->getId() : 0);
                            $log->setEntity('User');
                            $log->setField($keyField);
                            $log->setEntityId($entity->getId());
                            if ($keyField == 'location') {
                                if (!empty($field[0]))
                                    $log->setOldvalue($field[0]->getTitle());
                                if (!empty($field[1]))
                                    $log->setNewvalue($field[1]->getTitle());
                            } else {
                                $log->setOldvalue($field[0]);
                                $log->setNewvalue($field[1]);
                            }
                            $log->setUserId($userId);
                            $em->persist($log);
                            $classMetadata = $em->getClassMetadata(AAVVLog::class);
                            $uow->computeChangeSet($classMetadata, $log);
                        }
                    }
                }

                if ($entity instanceof AAVVAddress) {
                    foreach ($uow->getEntityChangeSet($entity) as $keyField => $field) {
                        if(!in_array($keyField, ['aavv', 'id'])) {
                            $log = new AAVVLog();
                            $log->setType('CREATE');
                            $log->setDate(new \DateTime('now'));
                            $log->setAavvId($entity->getAavv()->getId());
                            $log->setEntity('Address');
                            $log->setField($keyField);
                            $log->setEntityId($entity->getId());
                            if ($keyField == 'location') {
                                $log->setNewvalue($field[1]->getTitle());
                            } else {
                                $log->setOldvalue($field[0]);
                                $log->setNewvalue($field[1]);
                            }

                            $log->setUserId($userId);
                            $em->persist($log);
                            $classMetadata = $em->getClassMetadata(AAVVLog::class);
                            $uow->computeChangeSet($classMetadata, $log);
                        }
                    }
                }
            }
            //DELETES
            foreach ($uow->getScheduledEntityDeletions() as $keyEntity => $entity) {
                if ($entity instanceof User) {
                    $log = new AAVVLog();
                    $log->setType('DELETE');
                    $log->setDate(new \DateTime('now'));
                    $log->setEntity('USER');
                    $log->setField('all');
                    $log->setEntityId($entity->getId());
                    $log->setUserId($userId);
                    $em->persist($log);
                    $classMetadata = $em->getClassMetadata(AAVVLog::class);
                    $uow->computeChangeSet($classMetadata, $log);
                }
            }


            //UPDATES
            foreach ($uow->getScheduledEntityUpdates() as $keyEntity => $entity) {
                //Si es una agencia de viajes
                if ($entity instanceof AAVV) {
                    //die(var_dump($uow->getEntityChangeSet($entity)));
                    foreach ($uow->getEntityChangeSet($entity) as $keyField => $field) {
                        if(!in_array($keyField, ['aavv', 'id', 'subdomain'])) {
                            if ($keyField == 'coordinates') {
                                if (!empty($field[0]['latitude']) && ($field[0]['latitude'] != $field[1]['latitude'])) {
                                    $log = new AAVVLog();
                                    $log->setType('UPDATE');
                                    $log->setDate(new \DateTime('now'));
                                    $log->setEntity('AAVV');
                                    $log->setField('latitude');
                                    $log->setEntityId($entity->getId());
                                    if (!empty($field[0]))
                                        $log->setOldvalue($field[0]['latitude']);
                                    $log->setNewvalue($field[1]['latitude']);
                                    $log->setUserId($userId);
                                    $em->persist($log);
                                    $classMetadata = $em->getClassMetadata(AAVVLog::class);
                                    $uow->computeChangeSet($classMetadata, $log);

                                    $log2 = new AAVVLog();
                                    $log2->setType('UPDATE');
                                    $log2->setDate(new \DateTime('now'));
                                    $log2->setEntity('AAVV');
                                    $log2->setField('longitude');
                                    $log2->setEntityId($entity->getId());
                                    if (!empty($field[0]))
                                        $log2->setOldvalue($field[0]['longitude']);
                                    $log2->setNewvalue($field[1]['longitude']);
                                    $log2->setUserId($userId);
                                    $em->persist($log2);
                                    $classMetadata = $em->getClassMetadata(AAVVLog::class);
                                    $uow->computeChangeSet($classMetadata, $log2);
                                }
                            } else {
                                $log = new AAVVLog();
                                $log->setType('UPDATE');
                                $log->setDate(new \DateTime('now'));
                                $log->setEntity('AAVV');
                                $log->setField($keyField);
                                $log->setEntityId($entity->getId());
                                $field1 = ($field[0] instanceof \DateTime) ?
                                    $field[0]->format('Y-m-d h:i:s') :
                                    $field[0];
                                $field2 = ($field[1] instanceof \DateTime) ?
                                    $field[1]->format('Y-m-d h:i:s') :
                                    $field[1];
                                $log->setOldvalue($field1);
                                $log->setNewvalue($field2);
                                $log->setUserId($userId);
                                $em->persist($log);
                                $classMetadata = $em->getClassMetadata(AAVVLog::class);
                                $uow->computeChangeSet($classMetadata, $log);
                            }
                        }
                    }
                }
                // Si es un usuario
                if ($entity instanceof AAVVProfile) {
                    //die(var_dump($uow->getEntityChangeSet($entity)));
                    foreach ($uow->getEntityChangeSet($entity) as $keyField => $field) {
                        if ($keyField != 'profileorder') {
                            $log = new AAVVLog();
                            $log->setType('UPDATE');
                            $log->setDate(new \DateTime('now'));
                            $log->setAavvId($entity->getAavv()->getId());
                            $log->setEntity('User');
                            $log->setField($keyField);
                            $log->setEntityId($entity->getId());
                            if ($keyField == 'location') {
                                if (!empty($field[0]))
                                    $log->setOldvalue($field[0]->getTitle());
                                $log->setNewvalue($field[1]->getTitle());
                            } else {
                                $log->setOldvalue($field[0]);
                                $log->setNewvalue($field[1]);

                                $field1 = ($field[0] instanceof \DateTime) ?
                                    $field[0]->format('Y-m-d h:i:s') :
                                    $field[0];
                                $field2 = ($field[1] instanceof \DateTime) ?
                                    $field[1]->format('Y-m-d h:i:s') :
                                    $field[1];
                                $log->setOldvalue($field1);
                                $log->setNewvalue($field2);
                            }
                            $log->setUserId($userId);
                            $em->persist($log);
                            $classMetadata = $em->getClassMetadata(AAVVLog::class);
                            $uow->computeChangeSet($classMetadata, $log);
                        }
                    }
                }
                //Si es alguna de las direcciones de la agencia de viajes
                if ($entity instanceof AAVVAddress) {
                    //die(var_dump($uow->getEntityChangeSet($entity)));
                    foreach ($uow->getEntityChangeSet($entity) as $keyField => $field) {
                        $log = new AAVVLog();
                        $log->setType('UPDATE');
                        $log->setDate(new \DateTime('now'));
                        $log->setAavvId($entity->getAavv()->getId());
                        $log->setEntity('Address');
                        $log->setField($keyField);
                        $log->setEntityId($entity->getId());
                        if ($keyField == 'location') {
                            $log->setOldvalue($field[0]->getTitle());
                            $log->setNewvalue($field[1]->getTitle());
                        } else {
                            $log->setOldvalue($field[0]);
                            $log->setNewvalue($field[1]);
                        }

                        $log->setUserId($userId);
                        $em->persist($log);
                        $classMetadata = $em->getClassMetadata(AAVVLog::class);
                        $uow->computeChangeSet($classMetadata, $log);
                    }
                }
            }
        }
    }
}
