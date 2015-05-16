<?php

namespace Majora\Framework\Serializer\Model;

/**
 * Interface to implements on all
 * scopable models.
 */
interface SerializableInterface extends ScopableInterface
{
    /**
     * has to return an array representation of model.
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
    public function serialize($scope = 'default');

    /**
     * hydrate model from an array.
     *
     * @param array $objectData model fields as array
     *
     * @return self
     */
    public function deserialize(array $objectData);
}
