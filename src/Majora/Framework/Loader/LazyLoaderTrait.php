<?php

namespace Majora\Framework\Loader;

use Majora\Framework\Model\LazyPropertiesInterface;

/**
 * Implements entity lazy loading delegates hydration.
 */
trait LazyLoaderTrait
{
    /**
     * Hydrate lazy loads delegate into given Collectionnable, if enabled and if
     * entity supports it.
     *
     * @param LazyPropertiesInterface $object (not hinted to help notation and custom exception)
     *
     * @return LazyPropertiesInterface
     */
    private function loadDelegates($entity)
    {
        if (!$this instanceof LazyLoaderInterface) {
            return $entity;
        }
        if (!$entity instanceof LazyPropertiesInterface) {
            throw new \InvalidArgumentException(sprintf('%s objects cannot be hydrated with properties loading delegates without implementing %s.',
                get_class($entity),
                LazyPropertiesInterface::class
            ));
        }

        // global handler (deprecated)
        if (isset($proxies[$loaderClass = static::class])) {
            @trigger_error(
                sprintf('Global loader delegate is deprecated and will be removed in 1.6. Make "%s" loader invokable instead, entity to handle is given at first parameter.',
                    static::class
                ),
                E_USER_DEPRECATED
            );
            $proxies[$loaderClass]($entity);
            unset($proxies[$loaderClass]);
        }

        // define delegates into object
        $entity->registerLoaders(
            $this->getLoadingDelegates()
        );

        // global handler
        if (is_callable($this)) {
            $this($entity);
        }

        return $entity;
    }
}
