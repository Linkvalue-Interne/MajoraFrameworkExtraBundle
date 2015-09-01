<?php

namespace Majora\Framework\WebSocket\Client;

use Hoa\Websocket\Client as HoaClient;
use Majora\Framework\Log\LoggableTrait;
use Majora\Framework\WebSocket\Client\ClientInterface;

/**
 * Wrapper for HoaWebSocketClient
 */
class Client implements ClientInterface
{
    use LoggableTrait;

    /**
     * @var HoaClient
     */
    protected $webSocketClient;

    /**
     * construct
     *
     * @param HoaClient $client
     */
    public function __construct(HoaClient $webSocketClient)
    {
        $this->webSocketClient = $webSocketClient;
    }

    /**
     * Send a message to websocket server throught wrapped client
     *
     * @param string $action
     */
    public function send($event, array $data = array())
    {
        $eventData = array(
            'event' => $event,
            'data' => $data
        );

        $this->log('info', 'Send event throught websocket.', array(
            'event' => $event
        ));
        $this->log('debug', 'Websocket event data.', $eventData);

        $this->webSocketClient->connect();
        $this->webSocketClient->send(json_encode($eventData));
        $this->webSocketClient->close();
    }
}
