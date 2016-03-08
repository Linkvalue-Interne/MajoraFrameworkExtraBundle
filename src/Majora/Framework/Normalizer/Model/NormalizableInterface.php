<?php

namespace Majora\Framework\Normalizer\Model;

/**
 * Interface to implements on normalizable models.
 */
interface NormalizableInterface
{
    /**
     * Returns an indexed list of views of model as a list of fields or accessible methods.
     *
     * @example
     *    return array(
     *        'default'        => array('id', 'code', 'label'),
     *        'plain_field'    => 'id',
     *        'related_scope'  => array('@default', 'related_entity@related_scope', 'created_at', 'updated_at'),
     *        'optional'       => array('@related_scope', '?optional')
     *    );
     *
     * @return array
     */
    public static function getScopes();

    /**
     * Returns an array representation of model, matching given scope, if defined.
     *
     * @param string $scope optional scope of data
     *
     * @example
     *    return array(
     *        'id'        => $this->getId(),
     *        'firstname' => $this->getFirstname(),
     *        'skills'    => $this->getSkills()->serialize()
     *    );
     *
     * @return array
     *
     * @throws Majora\Framework\Normalizer\Exception\ScopeNotFoundException
     */
    public function normalize($scope = 'default');

    /**
     * Hydrate model from given array.
     *
     * @param array $objectData model fields as array
     *
     * @see Majora\Framework\Serializer\Model\SerializableTrait
     *
     * @return self
     */
    public function denormalize(array $objectData);
}
