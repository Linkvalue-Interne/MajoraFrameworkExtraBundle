<?php

namespace Majora\Framework\Domain;

use Majora\Framework\Domain\AbstractDomain;
use Majora\Framework\Domain\Action\ActionFactory;
use Majora\Framework\Domain\Action\PromisedAction;

/**
 * Base class for domains which uses distributed actions
 */
class ActionDispatcherDomain extends AbstractDomain
{
    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * Construct
     *
     * @param ActionFactory $actionFactory
     */
    public function __construct(ActionFactory $actionFactory)
    {
        $this->actionFactory = $actionFactory;
    }

    /**
     * Create and return a promise of $name action
     *
     * @param string  $name
     *
     * @return PromisedAction
     */
    public function getAction($name, ...$arguments)
    {
        return $this->actionFactory
            ->createAction($name)
            ->init(...$arguments)
        ;
    }

    /**
     * Resolve given action with given parameters
     *
     * @param string $name
     * @param string $arguments
     *
     * @return mixed
     */
    public function resolve($name, ...$arguments)
    {
        return $this->getAction($name, ...$arguments)
            ->resolve()
        ;
    }

    /**
     * Magic call implementation, proxy to resolve() method
     */
    public function __call($method, $arguments)
    {
        return $this->resolve($method, ...$arguments);
    }
}
