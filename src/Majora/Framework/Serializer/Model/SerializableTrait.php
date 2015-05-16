<?php

namespace Majora\Framework\Serializer\Model;

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
    public function serialize($scope = 'default')
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

        if (is_string($scopes[$scope])) {
            $method = sprintf('get%s', ucfirst($scopes[$scope]));

            return $this->$method();
        }

        $data  = array();
        $stack = array($scopes[$scope]);
        while (true) {
            $stackedField = array_shift($stack);
            foreach ($stackedField as $fieldConfig) {
                if (strpos($fieldConfig, '@') === false) {
                    $method = sprintf('get%s', ucfirst($fieldConfig));
                    $value  = $this->$method();

                    // serializable child object ?
                    if ($value instanceof SerializableInterface) {
                        $subScope = array_key_exists($scope, $value->getScopes()) ?
                            $scope : 'default'
                        ;
                        $value = $value->serialize($subScope);
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

                $method        = sprintf('get%s', ucfirst($field));
                $relatedEntity = $this->$method();

                // serialize child entity
                if ($relatedEntity instanceof SerializableInterface) {
                    $relatedEntity = $relatedEntity->serialize($includeScope);
                }

                $data[$field] = $relatedEntity;
            }

            if (empty($stack)) {
                break;
            }
        }

        return $data;
    }

    /**
     * @see SerializableInterface::deserialize()
     */
    public function deserialize(array $data)
    {
        foreach ($data as $property => $value) {
            if (!property_exists($this, $property)) {
                throw new \InvalidArgumentException(sprintf(
                    'Try to set "%s" property on a %s object which doesnt exists.',
                    $property,
                    get_class($this)
                ));
            }

            $setter = sprintf('set%s', ucfirst($property));
            if (!method_exists($this, $setter)) {
                $this->$property = $value;

                continue;
            }
            if (!is_array($value)) {
                $this->$setter($value);

                continue;
            }

            // extract setter class from type hinting
            $reflectionMethod = new \ReflectionMethod(get_class($this), $setter);
            $parameters       = $reflectionMethod->getParameters();
            $setParameter     = $parameters[0];

            if ($setParameter->isArray()) {
                $this->$setter($value);

                continue;
            }

            // non array hinting but no class either : interface or callable
            $classHinting = $setParameter->getClass();
            if (!$classHinting) {
                continue;
            }

            $instance = $classHinting->newInstance();
            $this->$setter($instance instanceof SerializableInterface ?
                $instance->deserialize($value) :
                $instance
            );
        }

        return $this;
    }
}
