<?php

namespace Majora\Framework\Api\Client;

/**
 * Define behavior for api clients.
 */
interface ApiClientInterface
{
    /**
     * Create and send a http request through http client, and return response as is.
     *
     * @param string $name
     * @param string $method
     * @param array  $query
     * @param array  $body
     * @param array  $options
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function send($name, $method, array $query = [], array $body = [], array $options = []);

    /**
     * Performs a cget query ("get" on a collection).
     *
     * @param array $query
     * @param array $options
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cget(array $query = [], array $options = []);

    /**
     * Performs a "get" query.
     *
     * @param array $query
     * @param array $options
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function get(array $query = [], array $options = []);

    /**
     * Performs a "post" query.
     *
     * @param array $query
     * @param array $body
     * @param array $options
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function post(array $query = [], array $body = [], array $options = []);

    /**
     * Performs a "put" query.
     *
     * @param array $query
     * @param array $body
     * @param array $options
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function put(array $query = [], array $body = [], array $options = []);

    /**
     * Performs a "delete" query.
     *
     * @param array $query
     * @param array $body
     * @param array $options
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(array $query = [], array $body = [], array $options = []);
}
