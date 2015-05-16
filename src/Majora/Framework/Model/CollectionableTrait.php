<?php

namespace Majora\Framework\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait for collectionable objects.
 */
trait CollectionableTrait
{
    /**
     * helper class to use for cast arrays to
     * collections of entities.
     *
     * @param ArrayCollection|array $data
     * @param string                $collectionClass
     *
     * @return EntityCollection
     */
    private function toCollection($data, $collectionClass)
    {
        $data = $data ?: array();

        if (!is_array($data) && !$data instanceof ArrayCollection) {
            throw new \InvalidArgumentException('Can transform only ArrayCollections or arrays.');
        }

        return is_object($data) && get_class($data) == $collectionClass ?
            $collectionClass :
            new $collectionClass(
                is_array($data) ? $data : $data->toArray()
            )
        ;
    }
}
