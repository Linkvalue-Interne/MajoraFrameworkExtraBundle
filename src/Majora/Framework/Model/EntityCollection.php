<?php

namespace Majora\Framework\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Majora\Framework\Serializer\Model\SerializableInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Base class for entity aggregation collection.
 */
class EntityCollection extends ArrayCollection implements SerializableInterface
{
    /**
     * return collectionned entity class
     *
     * @return string
     */
    protected function getEntityClass()
    {
        throw new \BadMethodCallException(sprintf('%s() method has to be defined in %s class.',
            __FUNCTION__, get_class($this)
        ));
    }

    /**
     * @see SerializableInterface::serialize()
     */
    public function serialize($scope = 'default', PropertyAccessorInterface $propertyAccessor = null)
    {
        return $this
            ->map(function(SerializableInterface $entity) use ($scope, $propertyAccessor) {
                return $entity->serialize($scope, $propertyAccessor);
            })
            ->toArray()
        ;
    }

    /**
     * @see SerializableInterface::deserialize()
     */
    public function deserialize(array $data, PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->clear();
        $entityClass = $this->getEntityClass();

        $entities = array_map(
            function (array $majoraEntityData) use ($entityClass, $propertyAccessor) {
                return (new $entityClass())->deserialize($majoraEntityData, $propertyAccessor);
            },
            $data
        );

        foreach ($entities as $entity) {
            $this->set($entity->getId(), $entity);
        }

        return $this;
    }

    /**
     * @see ScopableInterface::getScopes()
     */
    public function getScopes()
    {
        return array();
    }

    /**
     * filter given collection on given fields.
     *
     * @param array $filters
     *
     * @return EntityCollection
     */
    public function search(array $filters)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        return $this->filter(function (CollectionableInterface $entity) use ($filters, $propertyAccessor) {
            $res = true;
            foreach ($filters as $key => $value) {
                $current = $propertyAccessor->getValue($entity, $key);
                $res = $res && (is_array($value) ?
                    in_array($current, $value) :
                    $current == $value
                );
            }

            return $res;
        });
    }

    /**
     * extract the first $length elements from collection.
     *
     * @param int $length
     *
     * @return EntityCollection
     */
    public function chunk($length)
    {
        $chunkedData = array_chunk($this->toArray(), $length, true);

        return new self(empty($chunkedData) ? array() : $chunkedData[0]);
    }

    /**
     * @see ArrayCollection::slice()
     *
     * @return EntityCollection
     */
    public function cslice($offset, $length = null)
    {
        return new self($this->slice($offset, $length));
    }

    /**
     * index collection by given object field.
     *
     * @param string $field
     *
     * @return EntityCollection
     */
    public function indexBy($field)
    {
        $elements = $this->toArray();
        $this->clear();

        foreach ($elements as $element) {
            $method = sprintf('get%s', ucfirst($field));
            if (!is_callable(array($element, $method))) {
                throw new \InvalidArgumentException(sprintf(
                    'Cannot index %s elements on "%s" field. At least one element doesnt implements %s() method.',
                    get_class($this), $field, $method
                ));
            }

            $this->set($element->$method(), $element);
        }

        return $this;
    }

    /**
     * Sort collection with given closure
     *
     * @param \Closure $p
     *
     * @return EntityCollection
     */
    public function sort(\Closure $p)
    {
        $elements = $this->toArray();

        if (!uasort($elements, $p)) {
            throw new \InvalidArgumentException('Sort failed.');
        }

        return new self($elements);
    }

    /**
     * Reduce collection with given closure.
     * @link http://php.net/manual/en/function.array-reduce.php
     *
     * @param \Closure $p
     * @param mixed $initialValue
     *
     * @return mixed
     */
    public function reduce(\Closure $p, $initialValue = null)
    {
        return array_reduce($this->toArray(), $p, $initialValue);
    }
}
