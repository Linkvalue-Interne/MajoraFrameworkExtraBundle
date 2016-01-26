<?php

namespace Majora\Framework\Domain\Action;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Provides methods to use into action which are hydrated by forms
 */
trait DynamicActionTrait
{
    /**
     * @var ArrayCollection
     */
    protected $attributes;

    /**
     * Magic call implementation which forward to dynamic getter / setter
     */
    public function __call($method, $arguments)
    {
        switch (true) {

            // accessor ?
            case strpos($method, 'get') === 0 :
                return $this->_get(preg_filter(
                    '/^get(.+)/', '$1', $method
                ));

            // mutator ?
            case strpos($method, 'set') === 0 :
                return $this->_set(
                    preg_filter('/^set(.+)/', '$1', $method),
                    $arguments[0]
                );

            default:
                throw new \BadMethodCallException(sprintf('Method %s::%s() doesnt exists.',
                    get_class($this),
                    $method
                ));
        }
    }

    /**
     * Return attribute map
     *
     * @return ArrayCollection
     */
    private function getAttributes()
    {
        $this->attributes = $this->attributes ?: new ArrayCollection();

        return $this->attributes;
    }

    /**
     * Returns data under given key, null if undefined
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function _get($key)
    {
        return $this->getAttributes()->get($key);
    }

    /**
     * Stores given key as given value
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return self
     */
    protected function _set($key, $value)
    {
        $this->getAttributes()->set($key, $value);

        return $this;
    }

    /**
     * @see ScopableInterface::getScopes()
     */
    public static function getScopes()
    {
        return array();
    }

    /**
     * @see SerializableInterface::serialize()
     */
    public function serialize($scope = 'default', PropertyAccessorInterface $propertyAccessor = null)
    {
        return $this->getAttributes()->toArray();
    }

    /**
     * @see SerializableInterface::deserialize()
     */
    public function deserialize(array $objectData, PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->getAttributes()->add($objectData);
    }
}
