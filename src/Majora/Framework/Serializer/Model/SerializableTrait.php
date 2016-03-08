<?php

namespace Majora\Framework\Serializer\Model;

@trigger_error('The '.__NAMESPACE__.'\SerializableTrait class is deprecated and will be removed in 2.0. Use Majora\Framework\Normalizer\Model\NormalizableTrait instead.', E_USER_DEPRECATED);

use Majora\Framework\Normalizer\Model\NormalizableTrait;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Implements a generic serializable trait.
 *
 * @see SerializableInterface
 * @see ScopableInterface
 * @deprecated
 *
 * @method getScopes()
 */
trait SerializableTrait
{
    use NormalizableTrait;

    /**
     * @see SerializableInterface::serialize()
     */
    public function serialize($scope = 'default', PropertyAccessorInterface $propertyAccessor = null)
    {
        @trigger_error(sprintf('The method %s() is deprecated and will be removed in 2.0. Use Majora\Framework\Normalizer\Model\NormalizableTrait::normalize() instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->normalize($scope);
    }

    /**
     * @see SerializableInterface::deserialize()
     */
    public function deserialize(array $data, PropertyAccessorInterface $propertyAccessor = null)
    {
        @trigger_error(sprintf('The method %s() is deprecated and will be removed in 2.0. Use Majora\Framework\Normalizer\Model\NormalizableTrait::denormalize() instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->denormalize($data);
    }
}
