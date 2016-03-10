<?php

namespace Majora\Framework\Loader\Bridge\InMemory;

use Majora\Framework\Model\CollectionableInterface;
use Majora\Framework\Model\EntityCollection;
use Majora\Framework\Normalizer\MajoraNormalizer;

/**
 * Base trait for fixtures repository.
 */
trait InMemoryLoaderTrait
{
    /**
     * @var EntityCollection
     */
    protected $entityCollection;

    /**
     * @var MajoraNormalizer
     */
    protected $normalizer;

    /**
     * Construct.
     *
     * @param string           $collectionClass
     * @param MajoraNormalizer $normalizer
     */
    public function __construct($collectionClass, MajoraNormalizer $normalizer)
    {
        if (empty($collectionClass) || !class_exists($collectionClass)) {
            throw new \InvalidArgumentException(sprintf(
                'You must provide a valid EntityCollection class name, "%s" given.',
                $collectionClass
            ));
        }
        $this->entityCollection = new $collectionClass();
        if (!$this->entityCollection instanceof EntityCollection) {
            throw new \InvalidArgumentException(sprintf(
                'Provided class name is not an Majora\Framework\Model\EntityCollection, "%s" given.',
                $collectionClass
            ));
        }

        $this->normalizer = $normalizer;
    }

    /**
     * Asserts data source provided.
     *
     * @throws \RuntimeException If datastore is initialized
     */
    private function assertDatastoreIsReady()
    {
        if (!$this->entityCollection instanceof EntityCollection) {
            throw new \RuntimeException(sprintf(
                '%s datastore is not ready, setUp() method has to be called.',
                get_class($this)
            ));
        }
    }

    /**
     * Register given set of data into datastore.
     *
     * @param array $entityData
     */
    public function registerData(array $entityData)
    {
        $this->assertDatastoreIsReady();

        foreach ($entityData as $data) {
            $this->registerEntity($this->normalizer->normalize(
                $data,
                $this->entityCollection->getEntityClass()
            ));
        }
    }

    /**
     * Register a new Collectionable entity into datastore.
     *
     * @param CollectionableInterface $entity
     *
     * @throws \InvalidArgumentException If given object is not a supported type
     */
    public function registerEntity(CollectionableInterface $entity)
    {
        $this->assertDatastoreIsReady();

        if (!is_subclass_of($entity, $this->entityCollection->getEntityClass())) {
            throw new \InvalidArgumentException(sprintf('Only "%s" object allowed into "%s" store, "%s" given.',
                $this->entityCollection->getEntityClass(),
                get_class($this),
                get_class($entity)
            ));
        }

        $this->entityCollection->set($entity->getId(), $entity);
    }

    /**
     * {@inheritdoc}
     *
     * @see LoaderInterface::retrieveAll()
     */
    public function retrieveAll(array $filters = array(), $limit = null, $offset = null)
    {
        $this->assertDatastoreIsReady();

        $result = clone $this->entityCollection;
        if (!empty($filters)) {
            $result = $result->search($filters);
        }
        if ($offset) {
            $result = $result->cslice($offset, $limit);
        }
        if ($limit && !$offset) {
            $result = $result->chunk($limit);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see LoaderInterface::retrieveOne()
     */
    public function retrieveOne(array $filters = array())
    {
        return $this->retrieveAll()->first();
    }

    /**
     * {@inheritdoc}
     *
     * @see LoaderInterface::retrieve()
     */
    public function retrieve($id)
    {
        $this->assertDatastoreIsReady();

        return $this->entityCollection->get($id);
    }
}
