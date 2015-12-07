<?php

namespace Majora\Framework\Loader;

use Doctrine\Common\Collections\Collection;
use Majora\Framework\Loader\Bridge\Form\DataTransformerLoaderTrait;
use Majora\Framework\Loader\Bridge\Security\UserProviderLoaderTrait;
use Majora\Framework\Repository\RepositoryInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Base trait for loaders.
 */
trait LoaderTrait
{
    use DataTransformerLoaderTrait, UserProviderLoaderTrait;

    protected $entityRepository;
    protected $entityProperties;
    protected $entityClass;
    protected $collectionClass;
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

    /**
     * checks if loader is initialized.
     *
     * @throws RuntimeException if not configured
     */
    private function assertIsConfigured()
    {
        if ($this->entityClass
            && $this->collectionClass && $this->filterResolver
        ) {
            return;
        }

        throw new \RuntimeException(sprintf(
            '%s methods cannot be used, it hasnt been initialize through setUp() method.',
            __CLASS__
        ));
    }
}
