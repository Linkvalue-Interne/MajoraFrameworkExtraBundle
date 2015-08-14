<?php

namespace Majora\Framework\WebSocket\Server;

use Hoa\Core\Event\Bucket;
use Hoa\Websocket\Server as HoaServer;
use Majora\Framework\Log\LoggableTrait;
use Majora\Framework\WebSocket\Server\Exception\WebSocketException;

/**
 * Basic implementation of an HoaWebSocketServer, implements Hoa hooks
 *
 * Hooked methods :
 *  - open
 *  - message
 *  - binary-message
 *  - ping
 *  - close
 *  - error
 *
 * @link http://hoa-project.net/Fr/Literature/Hack/Websocket.html#Listeners
 */
class Server extends HoaServer
{
    use LoggableTrait;

    /**
     * Registers internal Hoa handlers
     */
    public function registerHandlers()
    {
        $this->on('open', array($this, 'onOpen'));
        $this->on('message', array($this, 'onMessage'));
        $this->on('binary-message', array($this, 'onBinaryMessage'));
        $this->on('ping', array($this, 'onPing'));
        $this->on('close', array($this, 'onClose'));
        $this->on('error', array($this, 'onError'));
    }

    /**
     * "open" WebSocket event handler
     *
     * @param Bucket $bucket
     */
    public function onOpen(Bucket $bucket)
    {
        $this->log('debug', 'New web socket connection.', array(
            'client' => $bucket->getSource()->getConnection()->getCurrentNode()->getId()
        ));
    }

    /**
     * "message" WebSocket event handler
     *
     * @param Bucket $bucket
     */
    public function onMessage(Bucket $bucket) { }

    /**
     * "binary-message" WebSocket event handler
     *
     * @param Bucket $bucket
     */
    public function onBinaryMessage(Bucket $bucket) { }

    /**
     * "ping" WebSocket event handler
     *
     * @param Bucket $bucket
     */
    public function onPing(Bucket $bucket) { }

    /**
     * "close" WebSocket event handler
     *
     * @param Bucket $bucket
     */
    public function onClose(Bucket $bucket)
    {
        $this->log('debug', 'Web socket closed.', array(
            'client' => $bucket->getSource()->getConnection()->getCurrentNode()->getId()
        ));
    }

    /**
     * "error" WebSocket event handler
     *
     * @param Bucket $bucket
     */
    public function onError(Bucket $bucket)
    {
        $data = $bucket->getData();
        $exception = $data['exception'];

        $this->log('critical', $exception->getMessage());

        throw new WebSocketException(
            $exception->getMessage(),
            $exception->getCode(),
            $exception
        );
    }
}
