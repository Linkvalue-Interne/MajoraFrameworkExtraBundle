<?php

namespace Majora\Framework\Domain\Action;

use GuzzleHttp\Promise\Promise;
use Majora\Framework\Domain\Action\ActionInterface;
use Majora\Framework\Domain\Action\ActionTrait;
use Majora\Framework\Domain\Action\DynamicActionTrait;
use Majora\Framework\Serializer\Model\SerializableInterface;

/**
 * Action implementation using Promises
 */
abstract class AbstractAction implements ActionInterface, SerializableInterface
{
    use DynamicActionTrait, ActionTrait;

    /**
     * Initialisation function
     */
    public function init()
    {
        return $this;
    }
}
