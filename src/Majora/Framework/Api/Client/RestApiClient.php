<?php

namespace Majora\Framework\Api\Client;

use GuzzleHttp\ClientInterface;
use Majora\Framework\Api\Request\RestApiRequestFactory;
use Majora\Framework\Log\LoggableTrait;

/**
 * Rest api client which use standard Http client to handle http calls.
 * Use and returns non parsed data from api calls.
 */
class RestApiClient
{
    use LoggableTrait;

    /**
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * @var RestApiRequestFactory
     */
    protected $requestFactory;

    /**
     * Construct
     *
     * @param ClientInterface       $httpClient
     * @param RestApiRequestFactory $requestFactory
     */
    public function __construct(ClientInterface $httpClient, RestApiRequestFactory $requestFactory)
    {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
    }

    /**
     * Create and send a http request throught http client, and return response as is
     *
     * @return Response
     */
    public function send(
        $name,
        $method,
        array $query = array(),
        array $body = array(),
        array $options = array()
    )
    {
        $options = $this->requestFactory->createRequestOptions($options);

        $body = $this->requestFactory->createRequestBodyData($body);
        if (!empty($body)) {
            $options = array_replace_recursive($options, array(
                'json' => $body
            ));
        }

        $response = $this->httpClient->request(
            $method,
            $this->requestFactory->createRequestUri($name, $query),
            $options
        );

        return $response;
    }

    /**
     * Performs a get query using "cget" factory presets
     *
     * @param array $query
     * @param array options
     *
     * @return Response
     */
    public function cget(array $query = array(), array $options = array())
    {
        return $this->send('cget', 'GET', $query, array(), $options);
    }

    /**
     * Performs a get query using "get" factory presets
     *
     * @param array $query
     * @param array options
     *
     * @return Response
     */
    public function get(array $query = array(), array $options = array())
    {
        return $this->send('get', 'GET', $query, array(), $options);
    }

    /**
     * Performs a post query using "post" factory presets
     *
     * @param array $query
     * @param array $body
     * @param array $options
     *
     * @return Response
     */
    public function post(array $query = array(), array $body = array(), array $options = array())
    {
        return $this->send('post', 'POST', $query, $body, $options);
    }

    /**
     * Performs a put query using "put" factory presets
     *
     * @param array $query
     * @param array $body
     * @param array $options
     *
     * @return Response
     */
    public function put(array $query = array(), array $body = array(), array $options = array())
    {
        return $this->send('put', 'POST', $query, $body, $options);
    }

    /**
     * Performs a delete query using "delete" factory presets
     *
     * @param array $query
     * @param array $body
     * @param array $options
     *
     * @return Response
     */
    public function delete(array $query = array(), array $body = array(), array $options = array())
    {
        return $this->send('delete', 'POST', $query, $body, $options);
    }
}
