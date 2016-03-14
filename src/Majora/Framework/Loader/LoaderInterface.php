<?php

namespace Majora\Framework\Loader;

use Majora\Framework\Model\EntityCollection;

/**
 * Base interface for all loaders.
 */
interface LoaderInterface
{
    /**
     * All entity metadata configuration are passed from this method.
     *
     * @param string $entityClass
     * @param array  $entityProperties
     * @param string $collectionClass
     */
    public function configureMetadata($entityClass, array $entityProperties, $collectionClass);

    /**
     * retrieve all entities in this repository.
     *
     * @param array $filters optionnal property => value filter map
     * @param int   $limit   optionnal limit of results
     * @param int   $offset
     *
     * @return EntityCollection
     */
    public function retrieveAll(array $filters = array(), $limit = null, $offset = null);

    /**
     * retrive one entity matching given filters throught this loader.
     *
     * @param array $filters optionnal property => value filter map
     *
     * @return object
     */
    public function retrieveOne(array $filters = array());

    /**
     * Retrieves a single entity by id.
     *
     * @param $id
     *
     * @return object
     */
    public function retrieve($id);
}
