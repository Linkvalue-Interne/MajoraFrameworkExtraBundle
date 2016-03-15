<?php

namespace Majora\Framework\Loader;

use Majora\Framework\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Base trait for loaders.
 *
 * @method retrieveById()
 */
trait LoaderTrait
{
    /**
     * @var RepositoryInterface
     */
    protected $entityRepository;

    /**
     * @var array
     */
    protected $entityProperties;

    /**
     * @var string
     */
    protected $entityClass;

    /**
     * @var string
     */
    protected $collectionClass;

    /**
     * @var OptionsResolver
     */
    protected $filterResolver;

    /**
     * @var \Closure
     */
    private $loadingDelegate;

    /**
     * setUp method.
     *
     * @param string              $entityClass
     * @param array               $entityProperties
     * @param string              $collectionClass
     * @param RepositoryInterface $entityRepository
     */
    public function setUp(
        $entityClass,
        array $entityProperties,
        $collectionClass,
        RepositoryInterface $entityRepository = null
    ) {
        @trigger_error(__METHOD__.'() is deprecated and will be removed in 2.0. Please use configureMetadata() instead.', E_USER_DEPRECATED);

        if ($entityRepository) {
            @trigger_error('Repository injection throught '.__METHOD__.'() is deprecated and will be removed in 2.0. Please inject it by constructor.', E_USER_DEPRECATED);

            $this->entityRepository = $entityRepository;
        }

        return $this->configureMetadata($entityClass, $entityProperties, $collectionClass);
    }

    /**
     * Checks if loader is properly configured.
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
            '%s methods cannot be used, it has not been initialize through configureMetadata() method.',
            __CLASS__
        ));
    }

    /**
     * @see LoaderInterface::configureMetadata()
     */
    public function configureMetadata($entityClass, array $entityProperties, $collectionClass)
    {
        $this->entityClass = $entityClass;
        $this->entityProperties = $entityProperties;
        $this->collectionClass = $collectionClass;

        $this->filterResolver = new OptionsResolver();
        foreach ($entityProperties as $propertyName) {
            $this->filterResolver->setDefined($propertyName);
        }
    }

    /**
     * Returns repository loading delegate.
     *
     * @return \Closure
     */
    public function getLoadingDelegate()
    {
        return $this->loadingDelegate = $this->loadingDelegate ?: function ($id) {
            return $this->retrieveById($id);
        };
    }
}
