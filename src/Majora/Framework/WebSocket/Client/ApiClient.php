<?php

namespace Majora\Framework\WebSocket\Client;

use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\ClientInterface as HttpClientInterface;
use Majora\Framework\Log\LoggableTrait;
use Majora\Framework\WebSocket\Client\SpoolableClientInterface;

/**
 * Websocket client which use Api post calls instead of
 * websocket protocol to send messages
 */
class ApiClient implements SpoolableClientInterface
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
     * @var ArrayCollection
     */
    protected $spooler;

    /**
     * Construct
     */
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->spooler = new ArrayCollection();
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
     * @see ClientInterface::spool()
     */
    public function spool($event, array $data = array())
    {
        $this->spooler->add(array(
            'event' => $event,
            'data' => $data
        ));
    }

    /**
     * @see ClientInterface::unleash()
     */
    public function unleash()
    {
        foreach ($this->spooler as $eventData) {
            $this->send($eventData['event'], $eventData['data']);
        }
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

        $this->httpClient->post($url, array('json' => $data));
    }

    /**
     * @see ClientInterface::disconnect()
     */
    public function disconnect()
    {
        // not needed for api implementation
    }
}
