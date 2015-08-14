<?php

namespace Majora\Framework\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Constraint class which materialize a check on given object class
 */
class InstanceOfConstraint extends Constraint
{
    public $message = 'Object of class "{{ object_class }}" is not a "{{ tested_class }}".';

    public $class;

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'class';
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return array('class');
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}
