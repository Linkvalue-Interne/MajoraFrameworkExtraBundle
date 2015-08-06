<?php

namespace Majora\Framework\Model;

use Doctrine\Common\Collections\Collection;

/**
 * Trait for collectionable objects.
 */
trait CollectionableTrait
{
    /**
     * helper method to use for cast arrays to
     * collections of entities.
     *
     * @param Collection|array $data
     * @param string           $collectionClass
     *
     * @return EntityCollection
     */
    protected function toCollection($data, $collectionClass)
    {
        if ($data instanceof $collectionClass) {
            return $data;
        }

        $data = $data ?: array();
        if (!is_array($data) && !$data instanceof Collection) {
            throw new \InvalidArgumentException('Can transform only Collections or arrays.');
        }

        return new $collectionClass(
            is_array($data) ? $data : $data->toArray()
        );
    }
}
