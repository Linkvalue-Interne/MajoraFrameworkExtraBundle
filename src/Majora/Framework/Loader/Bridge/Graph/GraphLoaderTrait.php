<?php

namespace Majora\Framework\Loader\Bridge\Graph;

use Majora\Framework\Loader\LoaderInterface;

/**
 * Trait to use into Graph loaders to get a simple implementation of LoaderInterface
 */
trait GraphLoaderTrait
{
    /**
     * @see LoaderInterface::retrieveAll()
     */
    public function retrieveAll(array $filters = array(), $limit = null, $offset = null)
    {
        throw new \BadMethodCallException(sprintf('Not yet implemented : %s::%s()', __CLASS__, __FUNCTION__));
    }

    /**
     * @see LoaderInterface::retrieveOne()
     */
    public function retrieveOne(array $filters = array())
    {
        throw new \BadMethodCallException(sprintf('Not yet implemented : %s::%s()', __CLASS__, __FUNCTION__));
    }

    /**
     * @see LoaderInterface::retrieve()
     */
    public function retrieve($id)
    {
        throw new \BadMethodCallException(sprintf('Not yet implemented : %s::%s()', __CLASS__, __FUNCTION__));
    }
}
