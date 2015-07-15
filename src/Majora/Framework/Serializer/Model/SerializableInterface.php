<?php

namespace Majora\Framework\Serializer\Model;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Interface to implements on all
 * scopable models.
 */
interface SerializableInterface extends ScopableInterface
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
