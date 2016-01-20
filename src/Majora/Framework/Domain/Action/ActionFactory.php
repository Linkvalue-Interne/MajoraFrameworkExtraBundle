<?php

namespace Majora\Framework\Domain\Action;

use Doctrine\Common\Collections\ArrayCollection;
use Majora\Framework\Domain\Action\ActionInterface;

/**
 * Factory class for domain actions
 */
class ActionFactory
{
    /**
     * @var ArrayCollection
     */
    protected $actions;

    /**
     * Construct
     *
     * @param array $prototypes
     */
    public function __construct(array $actions)
    {
        $this->actions = new ArrayCollection($actions);
    }

    /**
     * Creates and return a new action under given name
     *
     * @param string $name
     *
     * @return ActionInterface
     */
    public function createAction($name)
    {
        if (!$this->actions->containsKey($name)) {
            throw new \InvalidArgumentException(sprintf(
                'Any action registered under "%s" name, only [%s] are.',
                $name,
                implode('","', $this->actions->getKeys())
            ));
        }

        return clone $this->actions->get($name);
    }
}
