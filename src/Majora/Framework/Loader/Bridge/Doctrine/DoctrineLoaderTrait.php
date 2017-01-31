<?php

namespace Majora\Framework\Loader\Bridge\Doctrine;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use Majora\Framework\Loader\LoaderTrait;

/**
 * Trait to use into Doctrine loaders to get a simple implementation of LoaderInterface.
 *
 * @method getEntityRepository : EntityRepository
 * @method setEntityRepository(EntityRepository)
 *
 * @property $entityClass
 * @property $collectionClass
 * @property $filterResolver
 */
trait DoctrineLoaderTrait
{
    use LoaderTrait;

    /**
     * Construct.
     *
     * @param EntityRepository $entityRepository (optionnal)
     */
    public function __construct(EntityRepository $entityRepository = null)
    {
        if ($entityRepository) {
            @trigger_error('Repository constructor injection is deprecated for ORM implementation due to circular references with Doctrine events and will be removed in 2.0. Use setEntityRepository() instead.', E_USER_DEPRECATED);
            $this->setEntityRepository($entityRepository);
        }
    }

    /**
     * Checks if loader is properly configured.
     *
     * @throws \RuntimeException if not configured
     */
    private function assertIsConfigured()
    {
        if (!$this->entityClass || !$this->collectionClass || !$this->filterResolver) {
            throw new \RuntimeException(sprintf(
                '%s methods cannot be used while it has not been initialize through %s::configureMetadata().',
                static::class,
                static::class
            ));
        }
    }

    /**
     * Convert given array or Collection result set to managed entity collection class.
     *
     * @param array|Collection $result
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

            // simple related entity ?
            case is_object($result) && is_subclass_of($result, $this->entityClass) :
                $collection = new $this->collectionClass(array($result));
                break;

            // simple array ?
            case is_array($result) :
                $collection = new $this->collectionClass($result);
                break;

            default:
                $collection = new $this->collectionClass();
                break;
        }

        if (is_callable(array($this, 'onLoad'))) {
            @trigger_error(
                sprintf('%s::onLoad() call is deprecated and will be removed in 2.0. Make "%s" invokable instead if you require to custom every "%s" loaded by ORM.',
                    static::class,
                    static::class,
                    $this->entityClass
                ),
                E_USER_DEPRECATED
            );

            return $this->onLoad($collection);
        }

        return $collection;
    }

    /**
     * Create entity query.
     * Proxy to base query builder method to use to custom all queries from this loader.
     *
     * @param string $alias
     *
     * @return QueryBuilder
     */
    protected function createQuery($alias = 'entity')
    {
        return $this->getEntityRepository()
            ->createQueryBuilder($alias)
        ;
    }

    /**
     * create query an filter it with given data.
     *
     * @param array $filters
     *
     * @return Query
     */
    private function createFilteredQuery(array $filters)
    {
        $qb = $this->createQuery('entity');

        foreach ($filters as $field => $filter) {
            $qb->andWhere(is_array($filter)
                    ? sprintf('entity.%s in (:%s)', $field, $field)
                    : sprintf('entity.%s = :%s', $field, $field)
                )
                ->setParameter(sprintf(':%s', $field), $filter)
            ;
        }

        return $qb->getQuery();
    }

    /**
     * @see LoaderInterface::retrieveAll()
     */
    public function retrieveAll(array $filters = array(), $limit = null, $offset = null)
    {
        $this->assertIsConfigured();

        $query = $this->createFilteredQuery(
            $this->filterResolver->resolve($filters)
        );

        if ($limit) {
            $query->setMaxResults($limit);
        }
        if ($offset) {
            $query->setFirstResult($offset);
        }

        return $this->toEntityCollection(
            $query->getResult()
        );
    }

    /**
     * @see LoaderInterface::retrieveOne()
     */
    public function retrieveOne(array $filters = array())
    {
        $this->assertIsConfigured();

        $entity = $this->createFilteredQuery($filters)
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;

        if (is_callable(array($this, 'onLoad'))) {
            @trigger_error(
                sprintf('%s::onLoad() call is deprecated and will be removed in 2.0. Make "%s" invokable instead if you require to custom every "%s" loaded by ORM.',
                    static::class,
                    static::class,
                    $this->entityClass
                ),
                E_USER_DEPRECATED
            );

            return $this->onLoad($entity);
        }

        return $entity;
    }

    /**
     * @see LoaderInterface::retrieve()
     */
    public function retrieve($id)
    {
        return $this->retrieveOne(array('id' => $id));
    }
}
