<?php

namespace Majora\Bundle\FrameworkExtraBundle\WebSocket\Client;

use Majora\Framework\Event\BroadcastableEventInterface;
use Majora\Framework\WebSocket\Client\ClientInterface;
use Majora\Framework\WebSocket\Client\SpoolableClientInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * WebSocket client which can be bound to some broadcastable events
 * to send data throught websocket on registered event triggering
 */
class EventListenerWrapper implements ClientInterface, EventSubscriberInterface
{
    /**
     * @var ClientInterface
     */
    protected $websocketClient;

    /**
     * @var boolean
     */
    protected $spoolOnCommand;

    /**
     * @var boolean
     */
    protected $commandSpoolerActivated = false;

    /**
     * @var boolean
     */
    protected $spoolOnRequest;

    /**
     * @var boolean
     */
    protected $requestSpoolerActivated = false;

    /**
     * Construct
     *
     * @param ClientInterface $websocketClient
     * @param boolean         $spoolOnRequest
     * @param boolean         $spoolOnCommand
     */
    public function __construct(ClientInterface $websocketClient, $spoolOnRequest, $spoolOnCommand)
    {
        $this->websocketClient = $websocketClient;
        $this->spoolOnRequest = !empty($spoolOnRequest);
        $this->spoolOnCommand = !empty($spoolOnCommand);
    }

    /**
     * @see EventSubscriberInterface::getSubscribedEvents()
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest'),
            KernelEvents::TERMINATE => array('onKernelTerminate'),
            ConsoleEvents::COMMAND => array('onConsoleCommand'),
            ConsoleEvents::TERMINATE => array('onConsoleTerminate'),
        );
    }

    /**
     * Kernel request handler for spooling activation
     */
    public function onKernelRequest()
    {
        $this->requestSpoolerActivated = $this->spoolOnRequest;
    }

    /**
     * Console command handler for spooling activation
     */
    public function onConsoleCommand()
    {
        $this->commandSpoolerActivated = $this->spoolOnCommand;
    }

    /**
     * Send broadcastable data from given event throught registered websocket
     *
     * @param BroadcastableEventInterface $event
     */
    public function onBroadcastableEvent(BroadcastableEventInterface $event, $eventName)
    {
        $method = $this->requestSpoolerActivated || $this->commandSpoolerActivated ?
            'spool' : 'send'
        ;

        return $this->websocketClient->$method(
            $event->isBroadcasted() ? $event->getOriginName() : $eventName,
            $event->getData()
        );
    }

    /**
     * Request termination method handler
     */
    public function onKernelTerminate()
    {
        return $this->requestSpoolerActivated && $this->websocketClient instanceof SpoolableClientInterface ?
            $this->websocketClient->unleash() :
            null
        ;
    }

    /**
     * Console termination method handler
     */
    public function onConsoleTerminate()
    {
        return $this->commandSpoolerActivated && $this->websocketClient instanceof SpoolableClientInterface ?
            $this->websocketClient->unleash() :
            null
        ;
    }
}
