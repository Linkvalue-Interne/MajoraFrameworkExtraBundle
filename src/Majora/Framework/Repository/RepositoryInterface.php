<?php

namespace Majora\Framework\Repository;

use Majora\Framework\Model\CollectionableInterface;

/**
 * General repository interface.
 */
interface RepositoryInterface
{
    /**
     * save given entity data into persistence layer.
     *
     * @param CollectionableInterface $entity
     */
    public function persist(CollectionableInterface $entity);

    /**
     * delete given entity data from persistence layer.
     *
     * @param CollectionableInterface $entity
     */
    public function remove(CollectionableInterface $entity);
}
