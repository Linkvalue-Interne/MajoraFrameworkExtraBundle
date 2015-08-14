<?php

namespace Majora\Framework\WebSocket\Server;

use Doctrine\Common\Collections\ArrayCollection;
use Hoa\Core\Event\Bucket;
use Majora\Framework\WebSocket\Server\Server;

/**
 * Web socket server class which dispatch message to registered listeners
 */
class EventDispatcherServer extends Server
{
    /**
     * @var ArrayCollection
     */
    protected $listeners;

    /**
     * {@inheritdoc}
     */
    public function registerHandlers()
    {
        parent::registerHandlers();

        $this->listeners = new ArrayCollection(array());
    }

    /**
     * Register a listener for given metadata
     *
     * @param string $event
     * @param array  $metadata
     * @param Bucket $bucket
     */
    protected function registerListener(array $metadata, Bucket $bucket)
    {
        // store source to be able to notify it
        $node = $bucket->getSource()
            ->getConnection()
                ->getCurrentNode()
        ;

        $this->listeners->add(array(
            'metadata' => $metadata,
            'node' => $node
        ));
    }

    /**
     * Notify listeners of given data
     *
     * @param array  $data
     * @param Bucket $bucket
     */
    protected function notifyListeners(array $data, Bucket $bucket)
    {
        $this->listeners
            ->filter(function(array $listener) use ($data) {
                $metadata = $listener['metadata'];

                // listened event ?
                if (!empty($metadata['events'])) {
                    return in_array('*', $metadata['events']) // registered for broadcast ?
                        || in_array($data['event'], $metadata['events'])
                    ;
                }

                // matching metadata ?
                return empty($metadata) && empty($data['metadata'])
                    || $metadata == $data['metadata']
                ;
            })
            ->forAll(function($key, array $listener) use ($data, $bucket) {
                $node = $listener['node'];

                $bucket->getSource()->send(
                    json_encode($data),
                    $node
                );

                return true;
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function onMessage(Bucket $bucket)
    {
        $data = $bucket->getData();

        if (!($messageData = @json_decode($data['message'], true))
            || empty($messageData['event'])
        ) {
            $this->log('notice', 'Bad message recieved.', array(
                'message' => $data['message'],
                'json' => sprintf('%s: %s', json_last_error(), json_last_error_msg())
            ));

            return;
        }

        $this->log('info', 'Event recieved.', array(
            'event' => $messageData['event'],
            'sender' => $bucket->getSource()
                ->getConnection()
                    ->getCurrentNode()->getId()
        ));
        $this->log('debug', 'Event data recieved ', $messageData);

        return $messageData['event'] == 'subscribe' ?
            $this->registerListener($messageData['data'], $bucket) :
            $this->notifyListeners($messageData, $bucket)
        ;
    }

    /**
     * closing connection handler.
     *
     * @param Bucket $bucket
     */
    public function onClose(Bucket $bucket)
    {
        $node = $bucket->getSource()->getConnection()->getCurrentNode();

        $this->log('debug', 'Web socket closed.', array(
            'client' => $node->getId()
        ));
    }
}
