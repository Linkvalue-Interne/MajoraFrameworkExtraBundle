<?php

namespace Majora\Framework\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Simple implementation of BroadcastableEventInterface.
 */
class BroadcastableEvent extends Event implements BroadcastableEventInterface
{
    private $originName;
    private $isBroadcasted = false;

    /**
     * @see BroadcastableEventInterface::setOriginName()
     */
    public function setOriginName($originName)
    {
        $this->originName = $originName;
    }

    /**
     * return original name.
     *
     * @return string
     */
    public function getOriginName()
    {
        return $this->originName;
    }

    /**
     * @see BroadcastableEventInterface::getSubject()
     */
    public function getSubject()
    {
        throw new \BadMethodCallException(sprintf('%s::getSubject() has to be implemented.',
            get_class($this)
        ));
    }

    /**
     * @see BroadcastableEventInterface::getAction()
     */
    public function getAction()
    {
        throw new \BadMethodCallException(sprintf('%s::getAction() has to be implemented.',
            get_class($this)
        ));
    }

    /**
     * @see BroadcastableEventInterface::setBroadcasted()
     */
    public function setBroadcasted($broadcasted)
    {
        $this->isBroadcasted = !empty($broadcasted);
    }

    /**
     * @see BroadcastableEventInterface::isBroadcasted()
     */
    public function isBroadcasted()
    {
        return !empty($this->isBroadcasted);
    }
}
