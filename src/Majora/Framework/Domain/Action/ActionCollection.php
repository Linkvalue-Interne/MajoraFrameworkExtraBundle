<?php

namespace Majora\Framework\Domain\Action;

use Majora\Framework\Model\EntityCollection;

/**
 * Custom collection class for actions.
 */
class ActionCollection extends EntityCollection
{
    /**
     * Resolve all actions in a row and return result as array.
     *
     * @return array
     */
    public function resolve()
    {
        return $this
            ->map(function (ActionInterface $action) {
                return $action->resolve();
            })
            ->toArray()
        ;
    }
}
