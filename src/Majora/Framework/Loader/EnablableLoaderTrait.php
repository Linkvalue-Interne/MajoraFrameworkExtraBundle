<?php

namespace Majora\Framework\Loader;

/**
 * Enablable trait for loaders.
 *
 * @see Majora\Framework\Model\EnablableTrait
 * @see Majora\Framework\Loader\EnablableLoaderInterface
 */
trait EnablableLoaderTrait
{
    /**
     * @see EnablableLoaderInterface::retrieveEnabled()
     */
    public function retrieveEnabled(array $filters = array(), $limit = null, $offset = null)
    {
        return $this->retrieveAll(array_replace($filters, array('enabled' => true)), $limit, $offset);
    }

    /**
     * @see EnablableLoaderInterface::retrieveDisabled()
     */
    public function retrieveDisabled(array $filters = array(), $limit = null, $offset = null)
    {
        return $this->retrieveAll(array_replace($filters, array('enabled' => false)), $limit, $offset);
    }
}
