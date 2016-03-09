<?php

namespace Majora\Framework\Domain\Action;

use Majora\Framework\Normalizer\Model\NormalizableInterface;

/**
 * Interface for action classes
 *
 * @link https://schema.org/Action
 */
interface ActionInterface extends NormalizableInterface
{
    /**
     * Action resolving function
     *
     * @return mixed
     */
    public function resolve();
}
