<?php

namespace Majora\Framework\Repository\Graph;

use Majora\Framework\Model\CollectionableInterface;
use Majora\Framework\Repository\RepositoryInterface;

/**
 * Trait to use into Graph repositories to get a simple implementation of RepositoryInterface
 */
trait GraphRepositoryTrait
{
    /**
     * @see RepositoryInterface::persist()
     */
    public function persist(CollectionableInterface $entity)
    {

    }

    /**
     * @see RepositoryInterface::remove()
     */
    public function remove(CollectionableInterface $entity)
    {

    }
}
