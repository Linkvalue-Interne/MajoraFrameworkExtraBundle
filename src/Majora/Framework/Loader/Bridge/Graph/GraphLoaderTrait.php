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

    }

    /**
     * @see LoaderInterface::retrieveOne()
     */
    public function retrieveOne(array $filters = array())
    {

    }

    /**
     * @see LoaderInterface::retrieve()
     */
    public function retrieve($id)
    {

    }
}
