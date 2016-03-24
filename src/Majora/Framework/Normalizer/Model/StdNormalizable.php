<?php

namespace Majora\Framework\Normalizer\Model;

use Majora\Framework\Normalizer\Model\NormalizableInterface;

/**
 * Simple implementation of Normalizable with a StdClass
 */
class StdNormalizable extends \StdClass implements NormalizableInterface
{
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
        return (array) $this;
    }

    /**
     * @see NormalizableInterface::denormalize()
     */
    public function denormalize(array $data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }

        return $this;
    }
}
