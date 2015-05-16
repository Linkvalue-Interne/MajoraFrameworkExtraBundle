<?php

namespace Majora\Framework\Event;

/**
 * Interface to implement on broadcastable events.
 */
interface BroadcastableEventInterface
{
    /**
     * define event original name.
     *
     * @param string $name
     */
    public function setOriginName($name);

    /**
     * return original name.
     *
     * @return string
     */
    public function getOriginName();

    /**
     * return event related data.
     *
     * @return array
     */
    public function getData();

    /**
     * define is event is currently broadcasted.
     *
     * @param bool $broadcasted
     */
    public function setBroadcasted($broadcasted);

    /**
     * tests if event is currently broadcasted.
     *
     * @return bool
     */
    public function isBroadcasted();
}
