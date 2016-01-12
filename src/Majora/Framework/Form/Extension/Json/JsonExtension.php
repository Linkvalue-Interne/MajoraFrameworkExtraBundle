<?php

namespace Majora\Framework\Form\Extension\Json;

use Symfony\Component\Form\AbstractExtension;

/**
 * Integrates the JsonExtension with the Form library.
 */
class JsonExtension extends AbstractExtension
{
    protected function loadTypeExtensions()
    {
        return array(
            new Type\FormTypeJsonExtension(),
        );
    }
}
