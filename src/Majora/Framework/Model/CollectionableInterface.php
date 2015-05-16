<?php

namespace Majora\Framework\Model;

use Majora\Framework\Serializer\Model\SerializableInterface;

/**
 * Interface to implements objects
 * which can be used into entity collections.
 */
interface CollectionableInterface
    extends SerializableInterface
{
    /**
     * return object id.
     *
     * @return int
     */
    public function getId();
}
