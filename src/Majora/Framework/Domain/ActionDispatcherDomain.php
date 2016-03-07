<?php

namespace Majora\Framework\Domain;

use Majora\Framework\Domain\AbstractDomain;
use Majora\Framework\Domain\Action\ActionFactory;

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
     * @return ActionInterface
     */
    public function getAction($name, $relatedEntity = null, ...$arguments)
    {
        return $this->actionFactory
            ->createAction($name)
            ->deserialize(isset($arguments[0]) && is_array($arguments[0]) ?
                $arguments[0] :
                array()
            )
            ->init($relatedEntity)
        ;
    }
}
