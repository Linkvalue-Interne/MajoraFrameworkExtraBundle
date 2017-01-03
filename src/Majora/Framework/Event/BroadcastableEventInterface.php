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
     * return event related action
     *
     * @return mixed
     */
    public function getAction();

    /**
     * return event related subject
     *
     * @return mixed
     */
    public function getSubject();

    /**
     * define if event is currently broadcasted.
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
