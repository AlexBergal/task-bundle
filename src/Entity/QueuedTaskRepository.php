<?php

namespace Glooby\TaskBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Glooby\TaskBundle\Model\QueuedTaskInterface;

/**
 * @author Emil Kilhage
 */
class QueuedTaskRepository extends EntityRepository
{
    /**
     * @param string $name
     * @param \DateTime $executeAt
     * @return QueuedTaskInterface
     */
    public function getByNameAndExecuteAt($name, \DateTime $executeAt)
    {
        return $this->getEntityManager()
            ->createQuery('SELECT r
              FROM GloobyTaskBundle:QueuedTask r
              WHERE r.name = :name AND r.executeAt = :executeAt')
            ->setParameter('name', $name)
            ->setParameter('executeAt', $executeAt)
            ->useQueryCache(true)
            ->getSingleResult();
    }

    /**
     * @param string $name
     * @return QueuedTaskInterface
     */
    public function getByNameAndExecuteAtBeforeNow($name)
    {
        return $this->getEntityManager()
            ->createQuery('SELECT r
              FROM GloobyTaskBundle:QueuedTask r
              WHERE r.name = :name AND r.executeAt <= :now')
            ->setParameter('name', $name)
            ->setParameter('now', new \DateTime())
            ->useQueryCache(true)
            ->setMaxResults(1)
            ->getSingleResult();
    }

    /**
     * @param int $limit
     * @return QueuedTaskInterface[]
     */
    public function findQueued($limit)
    {
        return $this->getEntityManager()
            ->createQuery('SELECT r
              FROM GloobyTaskBundle:QueuedTask r
              WHERE r.status = :status AND r.executeAt <= :now
              ORDER BY r.executeAt ASC')
            ->setParameter('status', QueuedTaskInterface::STATUS_QUEUED)
            ->setParameter('now', new \DateTime())
            ->setMaxResults($limit)
            ->useQueryCache(true)
            ->getResult();
    }
}
