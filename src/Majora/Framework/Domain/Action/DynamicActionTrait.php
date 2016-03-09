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
        $this->getAttributes()->set($key, $value);

        return $this;
    }

    /**
     * Define given field on given object, if accessible
     *
     * @param mixed  $object
     * @param string $field
     *
     * @return mixed|null
     */
    protected function setIfDefined($object, $field)
    {
        $propertyAccessor = $this->getPropertyAccessor();
        if (!$this->_has($field)) {
            return $propertyAccessor->isReadable($object, $field) ?
                $propertyAccessor->getValue($object, $field) :
                null
            ;
        }

        $propertyAccessor->setValue(
            $object,
            $field,
            $value = $this->_get($field)
        );

        return $value;
    }

    /**
     * @see NormalizableInterface::getScopes()
     */
    public static function getScopes()
    {
        return array();
    }

    /**
     * @see NormalizableInterface::normalize()
     */
    public function normalize($scope = 'default')
    {
        $data = $this->getAttributes()->toArray();

        $scopes = $this->getScopes();
        if(empty($scopes[$scope])) {
            return $data;
        }

        $normalizedData = array();
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($scopes[$scope] as $field) {
            $normalizedData[$field] = $propertyAccessor->isReadable(
                    $data,
                    $propertyPath = strpos($field, '[') === 0 ?
                        $field :
                        sprintf('[%s]', $field)
                ) ?
                $propertyAccessor->getValue($data, $propertyPath) :
                null
            ;
        }

        return $normalizedData;
    }

    /**
     * @see NormalizableInterface::denormalize()
     */
    public function denormalize(array $objectData)
    {
        foreach ($objectData as $key => $value) {
            $this->_set($key, $value);
        }

        return $this;
    }

    /**
     * @see SerializableInterface::serialize()
     */
    public function serialize($scope = 'default', PropertyAccessorInterface $propertyAccessor = null)
    {
        @trigger_error(sprintf('The method %s() is deprecated and will be removed in 2.0. Use normalize() instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->normalize($scope);
    }

    /**
     * @see SerializableInterface::deserialize()
     */
    public function deserialize(array $data, PropertyAccessorInterface $propertyAccessor = null)
    {
        @trigger_error(sprintf('The method %s() is deprecated and will be removed in 2.0. Use denormalize() instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->denormalize($data);
    }
}
