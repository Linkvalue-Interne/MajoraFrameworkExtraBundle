<?php

namespace Majora\Framework\Model;

/**
 * Interface for enable / disable models
 */
interface EnablableInterface
{
    /**
     * enable entity
     *
     * @return self
     */
    public function enable();

    /**
     * disable entity
     *
     * @return self
     */
    public function disable();

    /**
     * tests if entity is enabled
     *
     * @return boolean
     */
    public function isEnabled();
}
