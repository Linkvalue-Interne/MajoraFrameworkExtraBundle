<?php

namespace Majora\Framework\WebSocket\Client\Wrapper;

use Majora\Framework\Event\BroadcastableEventInterface;
use Majora\Framework\WebSocket\Client\ClientInterface;

/**
 * WebSocket client which can be bound to some broadcastable events
 * to send data throught websocket on registered event triggering
 */
class EventListenerWrapper implements ClientInterface
{
    /**
     * @var ClientInterface
     */
    protected $websocketClient;

    /**
     * Construct
     *
     * @param ClientInterface $websocketClient
     */
    public function __construct(ClientInterface $websocketClient)
    {
        $this->websocketClient = $websocketClient;
    }

    /**
     * Send broadcastable data from given event throught registered websocket
     *
     * @param BroadcastableEventInterface $event
     */
    public function onBroadcastableEvent(BroadcastableEventInterface $event, $eventName)
    {
        return $this->websocketClient->send(
            $event->isBroadcasted() ? $event->getOriginName() : $eventName,
            $event->getData()
        );
    }
}
