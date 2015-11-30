<?php

namespace Majora\Framework\Loader;

/**
 * Enablable loader interface.
 */
interface EnablableLoaderInterface extends LoaderInterface
{
    /**
     * retrieveAll proxy to retrieve only enabled entities
     *
     * @see LoaderInterface::retrieveAll()
     */
    public function retrieveEnabled(array $filters = array(), $limit = null, $offset = null);

    /**
     * retrieveAll proxy to retrieve only disabled entities
     *
     * @see LoaderInterface::retrieveAll()
     */
    public function retrieveDisabled(array $filters = array(), $limit = null, $offset = null);
}
