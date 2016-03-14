<?php

namespace Majora\Framework\Model;

/**
 * Closure hydrating properties behavior definition.
 */
interface LazyPropertiesInterface
{
    /**
     * Register a loading delegate for given field.
     *
     * @param string   $field
     * @param \Closure $delegate
     */
    public function registerLoader($field, \Closure $delegate);

    /**
     * Register a map of field => loading delegate.
     *
     * @param array $loaders
     */
    public function registerLoaders(array $loaders);
}
