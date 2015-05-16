<?php

namespace Majora\Framework\Loader;

/**
 * Base interface for all loaders.
 */
interface LoaderInterface
{
    /**
     * retrieve all entities in this repository.
     *
     * @param array $filters optionnal property => value filter map
     * @param int   $limit   optionnal limit of results
     * @param int   $offset
     *
     * @return Iterable
     */
    public function retrieveAll(array $filters = array(), $limit = null, $offset = null);

    /**
     * Retrieves a single entity by id.
     *
     * @param $id
     *
     * @return Object
     */
    public function retrieve($id);
}
