<?php

namespace Majora\Framework\Model;

use Majora\Framework\Normalizer\Model\NormalizableInterface;

/**
 * Interface to implements objects
 * which can be used into entity collections.
 */
interface CollectionableInterface extends NormalizableInterface
{
    /**
     * return object id.
     *
     * @return int
     */
    public function getId();
}
