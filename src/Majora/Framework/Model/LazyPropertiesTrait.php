<?php

namespace Majora\Framework\Model;

/**
 * Trait which provides lazy load delegates call on fields.
 */
trait LazyPropertiesTrait
{
    /**
     * @var EntityCollection
     */
    private $delegatesCollection;

    /**
     * @see LazyPropertiesInterface::registerLoader()
     */
    public function registerLoader($field, \Closure $delegate)
    {
        if (!$this->delegatesCollection instanceof EntityCollection) {
            $this->delegatesCollection = new EntityCollection();
        }

        $this->delegatesCollection->set(strtolower($field), $delegate);
    }

    /**
     * @see LazyPropertiesInterface::registerLoaders()
     */
    public function registerLoaders(array $loaders)
    {
        foreach ($loaders as $field => $closure) {
            $this->registerLoader($field, $closure);
        }
    }

    /**
     * Load given field if able to, define his value and return it.
     *
     * @example
     *   public function getRelatedEntity()
     *   {
     *       return $this->load('relatedEntity');
     *   }
     *
     * @param string $field
     *
     * @return mixed|null field value if retrieved
     */
    protected function load($field)
    {
        if (!is_null($this->$field)
            || !$this->delegatesCollection->containsKey($field = strtolower($field))
        ) {
            return $this->$field;
        }

        $loader = $this->delegatesCollection->get($field);
        $this->delegatesCollection->remove($field);

        return $this->$field = $loader($this);
    }
}
