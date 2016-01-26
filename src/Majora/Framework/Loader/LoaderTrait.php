<?php

namespace Majora\Framework\Loader;

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
     * setUp method.
     *
     * @param string              $entityClass
     * @param array               $entityProperties
     * @param string              $collectionClass
     * @param RepositoryInterface $entityRepository (optionnal)
     */
    public function setUp(
        $entityClass,
        array $entityProperties,
        $collectionClass,
        RepositoryInterface $entityRepository = null
    ) {
        $this->entityClass      = $entityClass;
        $this->entityProperties = $entityProperties;
        $this->collectionClass  = $collectionClass;
        $this->entityRepository = $entityRepository;

        $this->filterResolver = new OptionsResolver();
        foreach ($entityProperties as $propertyName) {
            $this->filterResolver->setDefined($propertyName);
        }
    }
}
