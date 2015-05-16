<?php

namespace Majora\Framework\Repository\Fixtures;

use Majora\Framework\Model\CollectionableInterface;
use Majora\Framework\Model\EntityCollection;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Base trait for fixtures repository.
 */
trait FixturesRepositoryTrait
{
    protected $data;

    /**
     * setUp method.
     * define data source, and parse it to given collection class.
     *
     * @param array               $dataSource
     * @param string              $collectionClass
     * @param SerializerInterface $serializer
     */
    public function setUp(
        array               $dataSource,
        $collectionClass,
        SerializerInterface $serializer
    ) {
        if (empty($collectionClass) || !class_exists($collectionClass)) {
            throw new \InvalidArgumentException(sprintf(
                'You must provide a valid class name, "%s" given.',
                $collectionClass
            ));
        }

        $reflect = new \ReflectionClass($collectionClass);
        if (!$reflect->isSubclassOf('Majora\Framework\Model\EntityCollection')) {
            throw new \InvalidArgumentException(sprintf(
                'You must provide a "Majora\Framework\Model\EntityCollection" class name, "%s" given.',
                $collectionClass
            ));
        }

        $this->data = $serializer->deserialize(
            $dataSource, $collectionClass, 'array'
        );

        $this->data->indexBy('id');
    }

    /**
     * asserts data source provided.
     */
    private function assertDataLoaded()
    {
        if (!$this->data instanceof EntityCollection) {
            throw new \RuntimeException(sprintf(
                '%s methods cannot be used, it hasnt been initialize through setUp() method.',
                __CLASS__
            ));
        }
    }

    /**
     * {@inheritDoc}
     *
     * @see LoaderInterface::retrieveAll()
     */
    public function retrieveAll(array $filters = array(), $limit = null, $offset = null)
    {
        $this->assertDataLoaded();

        $result = $this->data;
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
     * {@inheritDoc}
     *
     * @see LoaderInterface::retrieve()
     */
    public function retrieve($id)
    {
        $this->assertDataLoaded();

        return $this->data->get($id);
    }

    /**
     * {@inheritDoc}
     *
     * @see RepositoryInterface::persist()
     */
    public function persist(CollectionableInterface $entity)
    {
        $this->assertDataLoaded();

        $this->data->set($entity->getId(), $entity);
    }

    /**
     * {@inheritDoc}
     *
     * @see RepositoryInterface::remove()
     */
    public function remove(CollectionableInterface $entity)
    {
        $this->assertDataLoaded();

        $this->data->remove($entity->getId());
    }
}
