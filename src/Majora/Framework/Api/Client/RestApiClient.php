<?php

namespace Majora\Framework\Api\Client;

use GuzzleHttp\ClientInterface;
use Majora\Framework\Api\Request\RestApiRequestFactory;
use Majora\Framework\Log\LoggableTrait;

/**
 * Rest api client which use standard Http client to handle http calls.
 * Use and returns non parsed data from api calls.
 */
class RestApiClient implements ApiClientInterface
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
     * Construct.
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
     * @see ApiClientInterface::send()
     */
    public function send(
        $name,
        $method,
        array $query = array(),
        array $body = array(),
        array $options = array()
    ) {
        return $this->httpClient->request(
            $method,
            $this->requestFactory->createRequestUri($name, $query),
            array_replace_recursive(
                $this->requestFactory->createRequestOptions($options),
                array('json' => $this->requestFactory->createRequestBodyData($body))
            )
        );
    }

    /**
     * @see ApiClientInterface::cget()
     */
    public function cget(array $query = array(), array $options = array())
    {
        return $this->send('cget', 'GET', $query, array(), $options);
    }

    /**
     * @see ApiClientInterface::get()
     */
    public function get(array $query = array(), array $options = array())
    {
        return $this->send('get', 'GET', $query, array(), $options);
    }

    /**
     * @see ApiClientInterface::post()
     */
    public function post(array $query = array(), array $body = array(), array $options = array())
    {
        return $this->send('post', 'POST', $query, $body, $options);
    }

    /**
     * @see ApiClientInterface::put()
     */
    public function put(array $query = array(), array $body = array(), array $options = array())
    {
        return $this->send('put', 'PUT', $query, $body, $options);
    }

    /**
     * @see ApiClientInterface::delete()
     */
    public function delete(array $query = array(), array $body = array(), array $options = array())
    {
        return $this->send('delete', 'DELETE', $query, $body, $options);
    }
}
