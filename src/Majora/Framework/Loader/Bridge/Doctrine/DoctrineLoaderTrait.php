<?php

namespace Majora\Framework\Loader\Bridge\Doctrine;

use Majora\Framework\Loader\LoaderTrait;

/**
 * Trait to use into Doctrine loaders to get a simple implementation of LoaderInterface
 */
trait DoctrineLoaderTrait
{
    use LoaderTrait;

    /**
     * checks if loader is initialized.
     *
     * @throws RuntimeException if not configured
     */
    private function assertIsConfigured()
    {
        if ($this->entityRepository && $this->entityClass
            && $this->collectionClass && $this->filterResolver
        ) {
            return;
        }

        throw new \RuntimeException(sprintf(
            '%s methods cannot be used, it hasnt been initialize through setUp() method.',
            __CLASS__
        ));
    }

    /**
     * hook which call on every entity or collection loaded through this loader
     *
     * @param object|EntityCollection $entity
     *
     * @return $object same entity
     */
    protected function onLoad($entity)
    {
        return $entity;
    }

    /**
     * Convert given array or Collection result set to managed entity collection class
     *
     * @param array|Collection $result [description]
     *
     * @return EntityCollection
     */
    protected function toEntityCollection($result)
    {
        switch (true) {

            // already a collection ?
            case is_object($result) && is_subclass_of($result, $this->collectionClass) :
                $collection = $result;
                break;

            // already a collection ?
            case $result instanceof Collection :
                $collection = new $this->collectionClass($result->toArray());
                break;

            case is_object($result) && is_subclass_of($result, $this->entityClass) :
                $collection = new $this->collectionClass(array($result));
                break;

            default:
                $collection = new $this->collectionClass();
                break;
        }

        return $this->onLoad($collection);
    }

    /**
     * Loads data from repository
     * then cast it to proper classes if not.
     *
     * @see LoaderInterface::retrieveAll()
     */
    public function retrieveAll(array $filters = array(), $limit = null, $offset = null)
    {
        $this->assertIsConfigured();

        return $this->toEntityCollection(
            $this->entityRepository->retrieveAll(
                $this->filterResolver->resolve($filters),
                $limit,
                $offset
            )
        );
    }

    /**
     * @see LoaderInterface::retrieveOne()
     */
    public function retrieveOne(array $filters = array())
    {
        return $this->retrieveAll($filters)->first();
    }

    /**
     * @see LoaderInterface::retrieve()
     */
    public function retrieve($id)
    {
        $this->assertIsConfigured();

        return $this->onLoad(
            $this->entityRepository->retrieve($id)
        );
    }
}
