<?php

namespace Majora\Framework\Normalizer\Model;

use Majora\Framework\Normalizer\MajoraNormalizer;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Implements a generic normalization, based on base object properties.
 */
trait NormalizableTrait
{
    /**
     * @see NormalizableInterface::normalize()
     */
    public function normalize($scope = 'default')
    {
        return MajoraNormalizer::createNormalizer()
            ->normalize($this, $scope)
        ;
    }

    /**
     * @see SerializableInterface::denormalize()
     */
    public function denormalize(array $data)
    {
        return MajoraNormalizer::createNormalizer()
            ->denormalize($data, $this)
        ;
    }
}
