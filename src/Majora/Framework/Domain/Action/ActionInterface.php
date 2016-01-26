<?php

namespace Majora\Framework\Domain\Action;

/**
 * Interface for action classes
 *
 * @link https://schema.org/Action
 */
interface ActionInterface
{
    /**
     * Action resolving function
     *
     * @return mixed
     */
    public function resolve();
}
