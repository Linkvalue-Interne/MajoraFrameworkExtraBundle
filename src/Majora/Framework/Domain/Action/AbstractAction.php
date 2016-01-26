<?php

namespace Majora\Framework\Domain\Action;

use Majora\Framework\Serializer\Model\SerializableInterface;

/**
 * Abstract action implementation
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
