<?php

namespace Majora\Framework\Loader;

/**
 * Interface to implement on loaders which can lazy load related data.
 */
interface LazyLoaderInterface
{
    /**
     * Returns a map of field => loading closure, foreach fields which are lazy loaded.
     *
     * @example
     *    return array(
     *         // use Loader FQCN as key allows to customize entiere entity on loading
     *        'Loader\F\Q\C\N' => function(CollectionnableInterface $entity) {
     *        },
     *        // use a property name will store this as loading delegate into loaded entities
     *        'foo' => function(CollectionnableInterface $entity) {
     *            return $this->fooLoader->retrieveById($entity->getFooId());
     *        }
     *    );
     *
     * @return array
     */
    public function getLoadingDelegates();
}
