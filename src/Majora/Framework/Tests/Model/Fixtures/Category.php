<?php

namespace Majora\Framework\Tests\Model\Fixtures;

/**
 * Class Category
 *
 * Used only for our tests.
 */
class Category
{
    /**
     * @var string
     */
    public $name;

    /**
     * Category constructor.
     *
     * @param null|string $name
     */
    public function __construct($name = null)
    {
        $this->name = $name;
    }
}
