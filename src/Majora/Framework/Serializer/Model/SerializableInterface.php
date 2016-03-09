<?php

namespace Majora\Framework\Serializer\Model;

@trigger_error('The '.__NAMESPACE__.'\ScopableInterface class is deprecated and will be removed in 2.0. Use Majora\Framework\Normalizer\Model\NormalizableInterface instead.', E_USER_DEPRECATED);

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Majora\Framework\Normalizer\Model\NormalizableInterface;

/**
 * Interface to implements on all serializable models.
 *
 * @deprecated
 */
interface SerializableInterface extends ScopableInterface, NormalizableInterface
{
    /**
     * has to return an array representation of model.
     *
     * @param string                    $scope            optionnal scope of data
     * @param PropertyAccessorInterface $propertyAccessor optionnal instance of property accessor (used for accessing data)
     *
     * @see Majora\Framework\Serializer\Model\ScopableInterface
     * @see Majora\Framework\Serializer\Model\SerializableTrait
     *
     * @example
     *    return array(
     *        'id'        => $this->getId(),
     *        'firstname' => $this->getFirstname(),
     *        'skills'    => $this->getSkills()->serialize()
     *    );
     *
     * @return array
     */
    public function serialize($scope = 'default', PropertyAccessorInterface $propertyAccessor = null);

    /**
     * hydrate model from an array.
     *
     * @param array                     $objectData       model fields as array
     * @param PropertyAccessorInterface $propertyAccessor optionnal instance of property accessor (used for accessing data)
     *
     * @see Majora\Framework\Serializer\Model\SerializableTrait
     *
     * @return self
     */
    public function deserialize(array $objectData, PropertyAccessorInterface $propertyAccessor = null);
}
