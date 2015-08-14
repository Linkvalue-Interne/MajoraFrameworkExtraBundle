<?php

namespace Majora\Framework\Validation\Constraint;

use Majora\Framework\Validation\Constraint\InstanceOfConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validator for InstanceOfConstraint
 */
class InstanceOfConstraintValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($object, Constraint $constraint)
    {
        if (!$constraint instanceof InstanceOfConstraint) {
            throw new UnexpectedTypeException($constraint, 'Majora\Framework\Validation\Constraint\InstanceOfConstraint');
        }

        if (null === $object) {
            return;
        }

        if (!is_object($object)) {
            throw new UnexpectedTypeException($object, 'object');
        }

        if (!is_a($object, $constraint->class)) {
            $this->context
                ->buildViolation($constraint->message)
                    ->setParameter('{{ object_class }}', $this->formatValue(get_class($object)))
                    ->setParameter('{{ tested_class }}', $this->formatValue($constraint->class))
                ->addViolation()
            ;
        }
    }
}
