<?php

namespace Majora\Framework\Validation;

use Majora\Framework\Model\CollectionableInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Custom exception class for validation exceptions.
 */
class ValidationException extends \InvalidArgumentException
{
    protected $entity;
    protected $report;
    protected $groups;

    /**
     * construct.
     *
     * @param CollectionableInterface          $entity
     * @param ConstraintViolationListInterface $report
     * @param array                            $groups
     */
    public function __construct(
        CollectionableInterface          $entity,
        ConstraintViolationListInterface $report,
        array                            $groups = null,
        $code     = null,
        $previous = null
    ) {
        $this->entity = $entity;
        $this->groups = $groups;
        $this->report = $report;

        parent::__construct(
            sprintf('Validation failed on %s#%s entity, on ["%s"] scopes.',
                get_class($entity),
                $entity->getId(),
                implode('", "', $groups ?: array())
            ),
            $code,
            $previous
        );
    }
}
