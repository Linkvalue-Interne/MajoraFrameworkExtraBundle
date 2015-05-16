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

    /**
     * return event related data.
     *
     * @return object
     */
    public function getData()
    {
        throw new \BadMethodCallException(sprintf(
            '%s method has to be implemented in class %s.',
            __FUNCTION__,
            get_class($this)
        ));
    }
}
