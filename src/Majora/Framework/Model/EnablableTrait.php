<?php

namespace Majora\Framework\Model;

/**
 * Trait for enable / disable models
 */
trait EnablableTrait
{
    protected $enabled;

    /**
     * @see EnablableInterface::enable()
     */
    public function enable()
    {
        $this->enabled = true;

        return $this;
    }

    /**
     * @see EnablableInterface::disable()
     */
    public function disable()
    {
        $this->enabled = false;

        return $this;
    }

    /**
     * @see EnablableInterface::isEnabled()
     */
    public function isEnabled()
    {
        return !empty($this->enabled);
    }
}
