<?php

namespace Majora\Framework\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Majora\Framework\Normalizer\MajoraNormalizer;
use Majora\Framework\Normalizer\Model\NormalizableInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Base class for entity aggregation collection.
 */
class EntityCollection extends ArrayCollection implements NormalizableInterface
{
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
}
