<?php

namespace Majora\Framework\Validation;

use Doctrine\Common\Collections\ArrayCollection;
use Majora\Framework\Model\CollectionableInterface;

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
     * @param CollectionableInterface                            $entity
     * @param FormErrorIterator|ConstraintViolationListInterface $report
     * @param array                                              $groups
     */
    public function __construct(
        CollectionableInterface $entity = null,
        $report = null,
        array $groups = null,
        $code     = null,
        $previous = null
    ) {
        $this->entity = $entity;
        $this->groups = $groups;
        $this->report = $report;

        $message = 'Validation failed';
        if ($this->entity) {
            $message .= sprintf(' on entity %s#%s',
                get_class($this->entity),
                $this->entity->getId()
            );
        }
        if (!empty($this->groups)) {
            $message .= sprintf(' for ["%s"] groups',
                implode('"', $this->groups)
            );
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * return failed entity
     *
     * @return CollectionableInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * return validation groups which are failing
     *
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * return violation list report
     *
     * @return ArrayCollection|FormErrorIterator|ConstraintViolationListInterface
     */
    public function getReport()
    {
        return $this->report ?
            $this->report :
            new ArrayCollection()
        ;
    }
}
