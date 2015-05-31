<?php

namespace Majora\Framework\Loader;

use Doctrine\Common\Collections\Collection;
use Majora\Framework\Repository\RepositoryInterface;
use Majora\Framework\Serializer\Model\SerializableInterface;

/**
 * Base trait for loaders.
 */
trait LoaderTrait
{
    protected $entityRepository;
    protected $entityClass;
    protected $collectionClass;

    /**
     * setUp method.
     *
     * @param RepositoryInterface $entityRepository
     * @param string              $entityClass
     * @param string              $collectionClass
     */
    public function setUp(
        RepositoryInterface $entityRepository,
        $entityClass,
        $collectionClass
    ) {
        $this->entityRepository = $entityRepository;
        $this->entityClass      = $entityClass;
        $this->collectionClass  = $collectionClass;

        $entity = new $entityClass();
        if (!$entity instanceof SerializableInterface) {
            throw new \InvalidArgumentException(
                'You must provide a Majora\Framework\Model\SerializableInterface class name.'
            );
        }
    }

    /**
     * checks if loader is initialized.
     *
     * @throws RuntimeException if not configured
     */
    private function assertIsConfigured()
    {
        if ($this->entityRepository && $this->entityClass && $this->collectionClass) {
            return;
        }

        throw new \RuntimeException(sprintf(
            '%s methods cannot be used, it hasnt been initialize through setUp() method.',
            __CLASS__
        ));
    }

    /**
     * Filter filters, only keep those who match related entity fields.
     *
     * @param array $filters
     *
     * @return array
     */
    private function cleanFilters(array $filters)
    {
        /** @var SerializableInterface $entity */
        $entity = new $this->entityClass();

        return array_intersect_key(
            $filters,
            array_flip(array_keys($entity->serialize()))
        );
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

        $collection = $this->entityRepository->retrieveAll(
            $this->cleanFilters($filters),
            $limit,
            $offset
        );

        return is_object($collection) && get_class($collection) == $this->collectionClass ?
            $collection :
            new $this->collectionClass(
                $collection instanceof Collection ?
                    $collection->toArray() :
                    $collection
            )
        ;
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

        return $this->entityRepository->retrieve($id);
    }
}
