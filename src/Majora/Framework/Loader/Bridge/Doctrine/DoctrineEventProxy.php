<?php

namespace Majora\Framework\Loader\Bridge\Doctrine;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Majora\Framework\Loader\Bridge\Doctrine\DoctrineLoaderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Proxy class to dispatch Doctrine events to proper loaders only
 * (Doctrine notify all listeners at each loaded entities, so we have to proxy it here, and lazy load dependencies)
 */
class DoctrineEventProxy implements EventSubscriber
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ArrayCollection
     */
    protected $loaderIds;

    /**
     * @var ArrayCollection
     */
    protected $loaders;

    /**
     * Construct
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->loaderIds = new ArrayCollection();
        $this->loaders = new ArrayCollection();
    }

    /**
     * Register a loader for given entity class
     *
     * @param string $entityClass
     * @param string $loaderId
     */
    public function registerDoctrineLoader($entityClass, $loaderId)
    {
        $this->loaderIds->set($entityClass, $loaderId);
    }

    public function getSubscribedEvents()
    {
        return array(DoctrineLoaderInterface::POST_LOAD_EVENT);
    }

    /**
     * "postLoad" Doctrine event handler, notify loaders if define for given event related entity
     *
     * @param LifecycleEventArgs $event
     */
    public function postLoad(LifecycleEventArgs $event)
    {
        // hack : events can provide Doctrine proxy items
        $entityClass = preg_replace('/^Proxies\\\__CG__\\\/', '', get_class($event->getEntity()));
        if (!$this->loaderIds->containsKey($entityClass)) {
            return;
        }
        if (!$loader = $this->loaders->get($entityClass)) {
            $loader = $this->container
                ->get($this->loaderIds->get($entityClass))
                ->getDelegate()
            ;

            $this->loaders->set($entityClass, $loader);
        }
        if (!$loader) {
            return;
        }

        $loader($event->getEntity());
    }
}
