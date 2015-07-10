<?php

namespace Majora\Framework\Loader;

use Doctrine\Common\Collections\Collection;
use Majora\Framework\Repository\RepositoryInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Base trait for loaders.
 */
trait LoaderTrait
{
    protected $entityRepository;
    protected $entityClass;
    protected $collectionClass;
    protected $filterResolver;

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

        $reflected = new \ReflectionClass($entityClass);
        if (!$reflected->implementsInterface('Majora\Framework\Model\CollectionableInterface')) {
            throw new \InvalidArgumentException(sprintf(
                'Cannot support "%s" class into "%s" : managed items have to be Majora\Framework\Model\CollectionableInterface.',
                $entityClass,
                __CLASS__
            ));
        }

        $this->filterResolver = new OptionsResolver();
        foreach ($reflected->getProperties() as $property) {
            $this->filterResolver->setDefined($property->getName());
        }
    }

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
     * Loads data from repository
     * then cast it to proper classes if not.
     *
     * @see LoaderInterface::retrieveAll()
     */
    public function retrieveAll(array $filters = array(), $limit = null, $offset = null)
    {
        $this->assertIsConfigured();

        $collection = $this->entityRepository->retrieveAll(
            $this->filterResolver->resolve($filters),
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

    /**
     * Model -> View
     *
     * @see DataTransformerInterface::transform()
     */
    public function transform($entity)
    {
        if (null === $entity) {
            return '';
        }
        if (get_class($entity) != $this->entityClass) {
            throw new \InvalidArgumentException(sprintf(
                'Unsupported entity "%s" into "%s" loader.',
                get_class($entity),
                __CLASS__
            ));
        }

        return $entity->getId();
    }

    /**
     * View -> Model
     *
     * @see DataTransformerInterface::reverseTransform()
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }
        if (!$entity = $this->retrieve($id)) {
            throw new TransformationFailedException(sprintf(
                '%s#%s cannot be found.',
                $this->entityClass,
                $id
            ));
        }

        return $entity;
    }
}
