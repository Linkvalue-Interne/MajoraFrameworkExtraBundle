<?php

namespace Majora\Framework\Repository\DoctrineOdm;

use Majora\Framework\Model\CollectionableInterface;

/**
 * Base trait for doctrine document repository.
 */
trait DoctrineRepositoryTrait
{
    /**
     * @see RepositoryInterface::persist()
     */
    public function persist(CollectionableInterface $majoraEntity)
    {
        $em = $this->getEntityManager();

        $em->persist($majoraEntity);
        $em->flush();
    }

    /**
     * @see RepositoryInterface::remove()
     */
    public function remove(CollectionableInterface $majoraEntity)
    {
        $em = $this->getEntityManager();

        $em->remove($majoraEntity);
        $em->flush();
    }
}
