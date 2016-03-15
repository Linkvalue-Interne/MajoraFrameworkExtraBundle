<?php

namespace Majora\Framework\Api\Client;

/**
 * Define behavior for api clients.
 */
interface ApiClientInterface
{
    /**
     * Create and send a http request throught http client, and return response as is.
     *
     * @param string $name
     * @param string $method
     * @param array  $query
     * @param array  $body
     * @param array  $options
     *
     * @return Response
     */
    public function send($name, $method, array $query = array(), array $body = array(), array $options = array());

    /**
     * Performs a cget query ("get" on a collection).
     *
     * @param array $query
     * @param array options
     *
     * @return Response
     */
    public function cget(array $query = array(), array $options = array());

    /**
     * Performs a "get" query.
     *
     * @param array $query
     * @param array options
     *
     * @return Response
     */
    public function get(array $query = array(), array $options = array());

    /**
     * Performs a "post" query.
     *
     * @param array $query
     * @param array $body
     * @param array $options
     *
     * @return Response
     */
    public function post(array $query = array(), array $body = array(), array $options = array());

    /**
     * Performs a "put" query.
     *
     * @param array $query
     * @param array $body
     * @param array $options
     *
     * @return Response
     */
    public function put(array $query = array(), array $body = array(), array $options = array());

    /**
     * Performs a "delete" query.
     *
     * @param array $query
     * @param array $body
     * @param array $options
     *
     * @return Response
     */
    public function delete(array $query = array(), array $body = array(), array $options = array());
}
