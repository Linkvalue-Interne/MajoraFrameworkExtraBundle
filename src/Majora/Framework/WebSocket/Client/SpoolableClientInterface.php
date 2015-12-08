<?php

namespace Majora\Framework\WebSocket\Client;

/**
 * Interface to implements on Spoolable websocket clients
 */
interface SpoolableClientInterface extends ClientInterface
{
    /**
     * Spool given event, waiting for unleash
     *
     * @param string $event
     * @param array  $data
     */
    public function spool($event, array $data = array());

    /**
     * Unleash all spooled events to web socket
     */
    public function unleash();
}
