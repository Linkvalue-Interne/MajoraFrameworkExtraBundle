<?php

namespace Majora\Framework\Loader\Bridge\Doctrine;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Majora\Framework\Loader\LazyLoaderInterface;
use Majora\Framework\Model\EntityCollection;
use Majora\Framework\Model\LazyPropertiesInterface;

/**
 * Proxy class to dispatch Doctrine events to proper loaders only
 * (Doctrine notify all listeners at each loaded entities, so we have to proxy it here, and lazy load dependencies).
 */
class DoctrineEventProxy
{
    /**
     * @var EntityCollection
     */
    protected $loaders;

    /**
     * @var EntityCollection
     */
    protected $unsupportedClasses;

    /**
     * Construct.
     */
    public function __construct()
    {
        $this->loaders = new EntityCollection();
        $this->unsupportedClasses = new EntityCollection();
    }

    /**
     * Register a loader for given entity class.
     *
     * @param string $entityClass
     * @param string $loader
     */
    public function registerDoctrineLazyLoader($entityClass, LazyLoaderInterface $loader)
    {
        if (!is_a($entityClass, LazyPropertiesInterface::class, true)) {
            throw new \InvalidArgumentException(sprintf(
                'Class %s has to implement %s to be able to lazy load her properties.',
                $entityClass,
                LazyPropertiesInterface::class
            ));
        }

        $this->loaders->set($entityClass, $loader);
    }

    /**
     * Retrieve a lazy loader for given class (or not).
     *
     * @param object $entityClass
     *
     * @return
     */
    private function getLoader($entity)
    {
        $entityClass = get_class($entity);

        if ($this->unsupportedClasses->containsKey($entityClass)) {
            return;
        }
        if ($this->loaders->containsKey($entityClass)) {
            return $this->loaders->get($entityClass);
        }
        foreach ($this->loaders as $handledClass => $loader) {
            if (is_a($entity, $handledClass)) {
                $this->loaders->set($entityClass, $loader);

                return $loader;
            }
        }

        $this->unsupportedClasses->set($entityClass, false);
    }

    /**
     * "postLoad" Doctrine event handler, notify loaders if define for given event related entity.
     *
     * @param LifecycleEventArgs $event
     */
    public function postLoad(LifecycleEventArgs $event)
    {
        if (!$loader = $this->getLoader($entity = $event->getEntity())) {
            return;
        }

        // define delegates into object
        $entity->registerLoaders(
            $loader->getLoadingDelegates()
        );

        // global delegate if able to
        if (is_callable($loader)) {
            $loader($entity);
        }
    }
}
