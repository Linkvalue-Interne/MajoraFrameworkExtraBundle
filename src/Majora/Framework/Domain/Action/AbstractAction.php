<?php

namespace Majora\Framework\Domain\Action;

use Majora\Framework\Serializer\Model\SerializableInterface;

/**
 * Abstract action implementation
 */
abstract class AbstractAction implements ActionInterface
{
    /**
     * Initialisation function
     */
    public function init()
    {
        return $this;
    }
}
