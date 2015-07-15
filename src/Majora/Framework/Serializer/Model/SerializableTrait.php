<?php

namespace Majora\Framework\Serializer\Model;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Implements a generic serializable trait.
 *
 * @see SerializableInterface
 * @see ScopableInterface
 */
trait SerializableTrait
{
    /**
     * @see SerializableInterface::getScopes()
     */
    abstract public function getScopes();

    /**
     * @see SerializableInterface::serialize()
     */
    public function serialize($scope = 'default', PropertyAccessorInterface $propertyAccessor = null)
    {
        $scopes = $this->getScopes();
        if (!isset($scopes[$scope])) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid scope for %s object, only [%s] supported, "%s" given.',
                __CLASS__,
                implode(', ', array_keys($scopes)),
                $scope
            ));
        }
        if (empty($scopes[$scope])) {
            return array();
        }

        $read = function ($property) use ($propertyAccessor) {
            if (!($propertyAccessor && $propertyAccessor->isReadable($this, $property))
                && !property_exists($this, $property)
            ) {
                throw new \InvalidArgumentException(sprintf(
                    'Unable to get "%s" property from a "%s" object, any existing property path to read it in.',
                    $property,
                    __CLASS__
                ));
            }

            return $propertyAccessor && $this->propertyAccessor->isReadable($this, $property) ?
                $this->propertyAccessor->getValue($this, $property) :
                $this->$property
            ;
        };

        if (is_string($scopes[$scope])) {
            return $read($scopes[$scope]);
        }

        $data  = array();
        $stack = array($scopes[$scope]);
        do {
            $stackedField = array_shift($stack);
            foreach ($stackedField as $fieldConfig) {
                if (strpos($fieldConfig, '@') === false) {
                    $value = $read($fieldConfig);

                    // serializable child object ?
                    if ($value instanceof SerializableInterface) {
                        $subScope = array_key_exists($scope, $value->getScopes()) ?
                            $scope : 'default'
                        ;
                        $value = $value->serialize($subScope, $propertyAccessor);
                    }

                    // date ?
                    if ($value instanceof \DateTime) {
                        $value = $value->format(\DateTime::ISO8601);
                    }

                    $data[$fieldConfig] = $value;

                    continue;
                }

                list($field, $includeScope) = explode('@', $fieldConfig);

                if (empty($field)) { // internal scope
                    array_unshift($stack, $scopes[$includeScope]);
                    continue;
                }

                // external scopes : first in, last in
                if (isset($data[$field])) {
                    continue;
                }

                $relatedEntity = $read($field);

                // serialize child entity
                if ($relatedEntity instanceof SerializableInterface) {
                    $relatedEntity = $relatedEntity->serialize($includeScope);
                }

                $data[$field] = $relatedEntity;
            }
        } while (!empty($stack));

        return $data;
    }

    /**
     * @see SerializableInterface::deserialize()
     */
    public function deserialize(array $data, PropertyAccessorInterface $propertyAccessor = null)
    {
        $write = function ($property, $value) use ($propertyAccessor) {
            if (!($propertyAccessor && $propertyAccessor->isWritable($this, $property))
                && !property_exists($this, $property)
            ) {
                throw new \InvalidArgumentException(sprintf(
                    'Unable to set "%s" property into a "%s" object, any existing property path to define it in.',
                    $property,
                    get_class($this)
                ));
            }

            if ($propertyAccessor && $propertyAccessor->isWritable($this, $property)) {
                $propertyAccessor->setValue($this, $property, $value);
            } else {
                $this->$property = $value;
            }
        };

        foreach ($data as $property => $value) {

            $setter = sprintf('set%s', ucfirst($property));
            if (!method_exists($this, $setter)) {
                $write($property, $value);
                continue;
            }

            // extract setter class from type hinting
            $reflectionMethod = new \ReflectionMethod(get_class($this), $setter);
            $parameters       = $reflectionMethod->getParameters();
            $setParameter     = $parameters[0];

            // scalar or array ?
            if (!$setParameter->getClass() || $setParameter->isArray()) {
                $write($property, $value);

                continue;
            }

            // nullable object ?
            if (empty($value)) {
                if ($setParameter->allowsNull()) {
                    $write($property, null);
                }

                continue;
            }

            // callable ?
            if (is_callable($value)) {
                if ($setParameter->isCallable()) {
                    $write($property, $value);
                }
            }

            $classHinting = $setParameter->getClass();

            $write(
                $property,
                $classHinting->implementsInterface('Majora\Framework\Serializer\Model\SerializableInterface') ?
                    $classHinting->newInstance()->deserialize($value, $propertyAccessor) : (
                        $classHinting->hasMethod('__construct') ?
                            $classHinting->newInstanceArgs(array($value)) :
                            $classHinting->newInstance()
                    )
            );
        }

        return $this;
    }
}
