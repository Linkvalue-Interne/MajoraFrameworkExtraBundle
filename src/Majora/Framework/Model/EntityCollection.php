<?php

namespace Majora\Framework\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Majora\Framework\Normalizer\MajoraNormalizer;
use Majora\Framework\Normalizer\Model\NormalizableInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Base class for entity aggregation collection.
 */
class EntityCollection extends ArrayCollection implements NormalizableInterface
{
    /**
     * @var PropertyAccessorInterface
     */
    private static $propertyAccessor;

    /**
     * Create and returns local property accessor.
     *
     * @return PropertyAccessorInterface
     */
    private function getPropertyAccessor()
    {
        return self::$propertyAccessor = self::$propertyAccessor
            ?: PropertyAccess::createPropertyAccessor()
        ;
    }

    /**
     * return collectionned entity class.
     *
     * @return string
     */
    public function getEntityClass()
    {
        throw new \BadMethodCallException(sprintf('%s() method has to be defined in %s class.',
            __FUNCTION__, static::class
        ));
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
        return $this
            ->map(function (NormalizableInterface $entity) use ($scope) {
                return $entity->normalize($scope);
            })
            ->toArray()
        ;
    }

    /**
     * @see NormalizableInterface::denormalize()
     */
    public function denormalize(array $data)
    {
        $this->clear();

        foreach ($data as $key => $majoraEntityData) {
            $this->set($key, MajoraNormalizer::createNormalizer()
                ->denormalize($majoraEntityData, $this->getEntityClass())
            );
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

    /**
     * filter given collection on given fields.
     *
     * @param array $filters
     *
     * @return EntityCollection
     */
    public function search(array $filters)
    {
        return $this->filter(function (CollectionableInterface $entity) use ($filters) {
            $res = true;
            foreach ($filters as $key => $value) {
                $current = $this->getPropertyAccessor()->getValue($entity, $key);
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

        return new static(empty($chunkedData) ? array() : $chunkedData[0]);
    }

    /**
     * @see ArrayCollection::slice()
     *
     * @return EntityCollection
     */
    public function cslice($offset, $length = null)
    {
        return new static($this->slice($offset, $length));
    }

    /**
     * Sort collection with given closure.
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

        return new static(array_values($elements));
    }

    /**
     * Reduce collection with given closure.
     *
     * @link http://php.net/manual/en/function.array-reduce.php
     *
     * @param \Closure $p
     * @param mixed    $initialValue
     *
     * @return mixed
     */
    public function reduce(\Closure $p, $initialValue = null)
    {
        return array_reduce($this->toArray(), $p, $initialValue);
    }

    /**
     * reads and returns value of given field into given element.
     *
     * @param CollectionableInterface $element
     * @param string                  $field
     *
     * @return mixed
     */
    private function getFieldValue(CollectionableInterface $element, $field)
    {
        return $this->getPropertyAccessor()->getValue($element, $field);
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
            $this->set(
                $this->getFieldValue($element, $field),
                $element
            );
        }

        return $this;
    }

    /**
     * Return value of inner objects given property as an array.
     *
     * This is an object version of array_column() method.
     * @link http://php.net/manual/fr/function.array-column.php
     *
     * @param string $column
     *
     * @return array
     */
    public function column($column)
    {
        return $this
            ->map(function (CollectionableInterface $entity) use ($column) {
                return $this->getFieldValue($entity, $column);
            })
            ->toArray()
        ;
    }

    /**
     * Create a flattened view of collection as a key value array.
     *
     * @param string $indexColumn
     * @param string $valueColumn
     *
     * @return array
     */
    public function flatten($indexColumn, $valueColumn)
    {
        return $this->reduce(
            function ($carry, CollectionableInterface $entity) use ($indexColumn, $valueColumn) {
                $carry[$this->getFieldValue($entity, $indexColumn)] = $this->getFieldValue($entity, $valueColumn);

                return $carry;
            },
            array()
        );
    }

    /**
     * Returns a string representation of given column values.
     *
     * @param string $column
     * @param string $slug
     *
     * @return string
     */
    public function display($column, $slug = '", "', $format = '["%s"]')
    {
        return sprintf($format, implode($slug, $this
            ->column($column)
        ));
    }
}
