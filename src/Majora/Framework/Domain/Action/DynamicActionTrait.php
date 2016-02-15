<?php

namespace Majora\Framework\Domain\Action;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\PropertyAccess\PropertyAccess;
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
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * Magic call implementation which forward to dynamic getter / setter
     */
    public function __call($method, $arguments)
    {
        switch (true) {

            // accessor ?
            case strpos($method, 'get') === 0 :
                return $this->_get(lcfirst(preg_filter(
                    '/^get(.+)/', '$1', $method
                )));

            // mutator ?
            case strpos($method, 'set') === 0 :
                return $this->_set(
                    lcfirst(preg_filter('/^set(.+)/', '$1', $method)),
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
     * Return internal property accessor
     *
     * @return PropertyAccessorInterface
     */
    private function getPropertyAccessor()
    {
        $this->propertyAccessor = $this->propertyAccessor ?:
            PropertyAccess::createPropertyAccessor()
        ;

        return $this->propertyAccessor;
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
     * Tests if given key exists
     *
     * @param string $key
     *
     * @return boolean
     */
    protected function _has($key)
    {
        return $this->getAttributes()->containsKey($key);
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
        $this->getAttributes()->set(ucfirst($key), $value);

        return $this;
    }

    /**
     * Define given field on given object, if accessible
     *
     * @param mixed  $object
     * @param string $field
     *
     * @return self
     */
    protected function setIfDefined($object, $field)
    {
        if (!$this->_has($field = ucfirst($field))) {
            return $this;
        }

        $this->getPropertyAccessor()->setValue(
            $object,
            $field,
            $this->_get($field)
        );

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
        foreach ($objectData as $key => $value) {
            $this->_set($key, $value);
        }

        return $this;
    }
}
