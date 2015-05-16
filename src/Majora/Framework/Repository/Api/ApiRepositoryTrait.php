<?php

namespace Majora\Framework\Repository\Api;

use Majora\Framework\Model\CollectionableInterface;

/**
 * Base trait for api repository.
 */
trait ApiRepositoryTrait
{
    /**
     * @see LoaderInterface::retrieveAll()
     */
    public function retrieveAll(array $filters = array(), $limit = null, $offset = null)
    {
        return array();
    }

    /**
     * @see LoaderInterface::retrieve()
     */
    public function retrieve($id)
    {
        return;
    }

    /**
     * @see RepositoryInterface::persist()
     */
    public function persist(CollectionableInterface $entity)
    {
        return;
    }

    /**
     * @see RepositoryInterface::remove()
     */
    public function remove(CollectionableInterface $entity)
    {
        return;
    }
}
