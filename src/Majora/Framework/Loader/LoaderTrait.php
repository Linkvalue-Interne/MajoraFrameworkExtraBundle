<?php

namespace Majora\Framework\Loader;

use Doctrine\Common\Collections\Collection;
use Majora\Framework\Model\EntityCollection;
use Majora\Framework\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Base trait for loaders.
 */
trait LoaderTrait
{
    /**
     * @var RepositoryInterface
     */
    protected $entityRepository;

    /**
     * @var string
     */
    protected $entityClass;

    /**
     * @var \ReflectionClass
     */
    protected $entityReflection;

    /**
     * @var string
     */
    protected $collectionClass;

    /**
     * @var OptionsResolver
     */
    protected $filterResolver;

    /**
     * setUp method.
     *
     * @param RepositoryInterface     $entityRepository
     * @param string                  $entityClass
     * @param string                  $collectionClass
     */
    public function setUp(
        RepositoryInterface $entityRepository,
        $entityClass,
        $collectionClass
    ) {
        $this->entityRepository = $entityRepository;
        $this->entityClass      = $entityClass;
        $this->collectionClass  = $collectionClass;

        $this->entityReflection = new \ReflectionClass($entityClass);
        if (!$this->entityReflection->implementsInterface('Majora\Framework\Model\CollectionableInterface')) {
            throw new \InvalidArgumentException(sprintf(
                'Cannot support "%s" class into "%s" : managed items have to be Majora\Framework\Model\CollectionableInterface.',
                $entityClass,
                __CLASS__
            ));
        }

        $this->filterResolver = new OptionsResolver();
        foreach ($this->entityReflection->getProperties() as $property) {
            $this->filterResolver->setDefined($property->getName());
        }
    }

    /**
     * checks if loader is initialized.
     *
     * @throws \RuntimeException if not configured
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
     * @return object same entity
     */
    protected function onLoad($entity)
    {
        return $entity;
    }

    /**
     * Convert given array or Collection result set to managed entity collection class
     *
     * @param array|Collection $result
     *
     * @return EntityCollection
     */
    protected function toEntityCollection($result)
    {
        return $this->onLoad(is_object($result) && get_class($result) == $this->collectionClass ?
            $result :
            new $this->collectionClass(
                $result instanceof Collection ?
                    $result->toArray() :
                    $result
            )
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
