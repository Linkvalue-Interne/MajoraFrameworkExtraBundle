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
     * @param array $actions
     */
    public function __construct(array $actions = array())
    {
        $this->actions = new ArrayCollection();
        foreach ($actions as $name => $action) {
            $this->registerAction($name, $action);
        }
    }

    /**
     * Register an action under given name
     *
     * @param string          $name
     * @param ActionInterface $action
     */
    public function registerAction($name, ActionInterface $action)
    {
        $this->actions->set($name, $action);
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
                'Any action registered under "%s" name, only ["%s"] are.',
                $name,
                implode('","', $this->actions->getKeys())
            ));
        }

        return clone $this->actions->get($name);
    }
}
