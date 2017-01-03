<?php

namespace Majora\Framework\Tests\Normalizer\Model;

use Majora\Framework\Normalizer\Model\StdNormalizable;

/**
 * Class StdNormalizableTest.
 *
 * @see \Majora\Framework\Normalizer\Model\StdNormalizable
 */
class StdNormalizableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test StdNormalizable::getScopes() method.
     */
    public function testGetScopes()
    {
        $this->assertTrue(is_array(StdNormalizable::getScopes()));
    }

    /**
     * Test StdNormalizable::normalize() method.
     */
    public function testNormalize()
    {
        $stdClass = new StdNormalizable();
        $stdClass->lastName = 'My last name';
        $stdClass->firstName = 'My first name';

        $this->assertEquals(
            [
                'lastName' => 'My last name',
                'firstName' => 'My first name',
            ],
            $stdClass->normalize()
        );
    }

    /**
     * Test StdNormalizable::denormalize() method.
     */
    public function testDenormalize()
    {
        $stdClass = new StdNormalizable();
        $stdClass->lastName = 'My last name';
        $stdClass->firstName = 'My first name';

        $stdClass2 = new StdNormalizable();

        $this->assertEquals(
            $stdClass,
            $stdClass2->denormalize(
                [
                    'lastName' => 'My last name',
                    'firstName' => 'My first name',
                ]
            )
        );
    }
}
