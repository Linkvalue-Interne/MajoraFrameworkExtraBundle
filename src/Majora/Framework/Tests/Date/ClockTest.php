<?php

namespace Majora\Framework\Tests\Date;

use Majora\Framework\Date\Clock;

/**
 * Class ClockTest.
 *
 * @see \Majora\Framework\Date\Clock
 */
class ClockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that constructor set up properties.
     */
    public function testConstructor()
    {
        $currentDateReflection = new \ReflectionProperty(Clock::class, 'currentDate');
        $currentDateReflection->setAccessible(true);

        $this->assertInstanceOf(\DateTime::class, $currentDateReflection->getValue(new Clock()));
    }

    /**
     * Test Clock::mock() method with DateTime argument.
     */
    public function testMockWithDateTime()
    {
        $clock = new Clock();

        $datetime = new \DateTime();

        $mockReflection = new \ReflectionMethod(Clock::class, 'mock');
        $mockReflection->setAccessible(true);
        $mockReflection->invokeArgs($clock, [$datetime]);

        $currentDateReflection = new \ReflectionProperty(Clock::class, 'currentDate');
        $currentDateReflection->setAccessible(true);

        $this->assertEquals($datetime, $currentDateReflection->getValue($clock));
    }

    /**
     * Test Clock::mock() method with DateTime argument as string.
     */
    public function testMockWithDateTimeString()
    {
        $clock = new Clock();

        $datetime = '2016-12-16 00:00:00';

        $mockReflection = new \ReflectionMethod(Clock::class, 'mock');
        $mockReflection->setAccessible(true);
        $mockReflection->invokeArgs($clock, [$datetime]);

        $currentDateReflection = new \ReflectionProperty(Clock::class, 'currentDate');
        $currentDateReflection->setAccessible(true);

        $this->assertEquals(new \DateTime($datetime), $currentDateReflection->getValue($clock));
    }

    /**
     * Test Clock::now() method.
     */
    public function testNow()
    {
        $clock = new Clock();

        $this->assertInstanceOf(\DateTime::class, $clock->now());
    }

    /**
     * Test Clock::now() method without format argument after mocked the date.
     */
    public function testNowWithoutFormatAndDateMocked()
    {
        $clock = new Clock();

        $datetime = new \DateTime();

        $mockReflection = new \ReflectionMethod(Clock::class, 'mock');
        $mockReflection->setAccessible(true);
        $mockReflection->invokeArgs($clock, [$datetime]);

        $this->assertEquals($datetime, $clock->now());
    }

    /**
     * Test Clock::now() method with format argument after mocked the date.
     */
    public function testNowWithFormatAndDateMocked()
    {
        $clock = new Clock();

        $datetime = new \DateTime();

        $mockReflection = new \ReflectionMethod(Clock::class, 'mock');
        $mockReflection->setAccessible(true);
        $mockReflection->invokeArgs($clock, [$datetime]);

        $this->assertEquals($datetime->format('Y-m-d'), $clock->now('Y-m-d'));
    }
}
