<?php

namespace Majora\Framework\Repository\InMemory;

use Majora\Framework\Model\CollectionableInterface;

/**
 * Base trait for in memory repository.
 */
trait InMemoryRepositoryTrait
{
    /**
     * {@inheritdac}.
     *
     * @see RepositoryInterface::persist()
     */
    public function persist(CollectionableInterface $entity)
    {
        return;
    }

    /**
     * {@inheritdoc}
     *
     * @see RepositoryInterface::remove()
     */
    public function remove(CollectionableInterface $entity)
    {
        return;
    }
}
