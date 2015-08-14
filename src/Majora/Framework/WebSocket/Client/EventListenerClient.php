<?php

namespace Majora\Framework\WebSocket\Client;

use Majora\Framework\Event\BroadcastableEventInterface;
use Majora\Framework\WebSocket\Client\Client;

/**
 * WebSocket client which can be bound to some broadcastable events
 * to send data throught websocket on registered event triggering
 */
class EventListenerClient extends Client
{
    /**
     * Send broadcastable data from given event throught registered websocket
     *
     * @param BroadcastableEventInterface $event
     */
    public function onBroadcastableEvent(BroadcastableEventInterface $event, $eventName)
    {
        return $this->send(
            $event->isBroadcasted() ? $event->getOriginName() : $eventName,
            $event->getData()
        );
    }
}
