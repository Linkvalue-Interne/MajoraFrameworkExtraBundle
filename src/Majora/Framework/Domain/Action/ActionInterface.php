<?php

namespace Majora\Framework\Domain\Action;

use Majora\Framework\Serializer\Model\SerializableInterface;

/**
 * Interface for action classes
 *
 * @link https://schema.org/Action
 */
interface ActionInterface extends SerializableInterface
{
    /**
     * Action resolving function
     *
     * @return mixed
     */
    public function resolve();
}
