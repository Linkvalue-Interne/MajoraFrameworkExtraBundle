<?php

namespace Majora\Framework\Loader\Bridge\DoctrineOdm;

use Doctrine\Common\Collections\Collection;
use Majora\Framework\Loader\LoaderTrait;
use Majora\Framework\Repository\DoctrineOdm\BaseDoctrineOdmRepository;

/**
 * Trait to use into Doctrine loaders to get a simple implementation of LoaderInterface.
 *
 * @property $entityRepository
 * @property $entityClass
 * @property $collectionClass
 * @property $filterResolver
 */
trait DoctrineOdmLoaderTrait
{
    use LoaderTrait;

    /**
     * Construct.
     *
     * @param BaseDoctrineRepository $entityRepository (optionnal)
     */
    public function __construct(BaseDoctrineOdmRepository $entityRepository = null)
    {
        $this->entityRepository = $entityRepository;
    }

    /**
     * Hook called with every entity or collection loaded through this loader.
     *
     * @param CollectionnableInterface|EntityCollection $entity
     *
     * @return $object same entity or collection
     */
    protected function onLoad($entity)
    {
        @trigger_error(__METHOD__.' is deprecated and will be removed in 2.0. Use full class delegate instead, see Majora\Framework\Loader\LazyLoaderInterface.', E_USER_DEPRECATED);

        return $entity;
    }

    /**
     * Convert given array or Collection result set to managed entity collection class.
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

        return $this->onLoad($collection);
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
        return $this->entityRepository->createQueryBuilder($alias);
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

        return $this->onLoad($this->createFilteredQuery($filters)
            ->getOneOrNullResult()
        );
    }

    /**
     * @see LoaderInterface::retrieve()
     */
    public function retrieve($id)
    {
        return $this->onLoad($this->retrieveOne(array('id' => $id)));
    }
}
