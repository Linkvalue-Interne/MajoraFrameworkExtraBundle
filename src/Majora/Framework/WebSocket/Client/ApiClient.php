<?php

namespace Majora\Framework\WebSocket\Client;

use GuzzleHttp\ClientInterface as HttpClientInterface;
use Majora\Framework\WebSocket\Client\ClientInterface;
use Majora\Framework\Log\LoggableTrait;

/**
 * Websocket client which use Api post calls instead of
 * websocket protocol to send messages
 */
class ApiClient implements ClientInterface
{
    use LoggableTrait;

    /**
     * @var HttpClientInterface
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $wsHttpEndPoint;

    /**
     * Construct
     */
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Define WebSocket Api endpoint
     *
     * @param string $wsHttpEndPoint
     */
    public function setWsApiEndpoint($route, $wsHttpEndPoint, array $defaultQueryParams = array())
    {
        $this->wsHttpEndPoint = $wsHttpEndPoint;
    }

    /**
     * @see ClientInterface::connect()
     */
    public function connect()
    {
        // not needed for api implementation
    }

    /**
     * @see ClientInterface::send()
     */
    public function send($event, array $data = array())
    {
        $url = sprintf('%s/%s', $this->wsHttpEndPoint, $event);

        $this->log('info', 'Send event throught websocket api.', array(
            'url' => $url,
            'event' => $event,
        ));
        $this->log('debug', 'Websocket event data.', $data);

        $this->httpClient->post($url, $data);
    }

    /**
     * @see ClientInterface::disconnect()
     */
    public function disconnect()
    {
        // not needed for api implementation
    }
}
