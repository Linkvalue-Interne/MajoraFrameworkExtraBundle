<?php

namespace Majora\Framework\Repository\Doctrine;

use Majora\Framework\Model\CollectionableInterface;

/**
 * Base trait for doctrine repository.
 */
trait DoctrineRepositoryTrait
{
    /**
     * return application entity manager.
     *
     * @return EntityManager
     */
    abstract protected function getEntityManager();

    /**
     * create a query builder.
     *
     * @param string $alias
     *
     * @return QueryBuilder
     */
    abstract public function createQueryBuilder($alias);

    /**
     * create entity query.
     * proxy to base query builder method to use to custom all
     * queries from using repository.
     *
     * @param string $alias
     *
     * @return QueryBuilder
     */
    protected function createQuery($alias = 'entity')
    {
        return $this->createQueryBuilder($alias);
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
            $qb->andWhere(is_array($filter) ?
                    sprintf('entity.%s in (:%s)', $field, $field) :
                    sprintf('entity.%s = :%s', $field, $field)
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
        $query = $this->createFilteredQuery($filters);

        if ($limit) {
            $query->setMaxResults($limit);
        }
        if ($offset) {
            $query->setFirstResult($offset);
        }

        return $query->getResult();
    }

    /**
     * @see LoaderInterface::retrieve()
     */
    public function retrieve($id)
    {
        return $this->createFilteredQuery(array('id' => $id))
            ->getOneOrNullResult()
        ;
    }

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
