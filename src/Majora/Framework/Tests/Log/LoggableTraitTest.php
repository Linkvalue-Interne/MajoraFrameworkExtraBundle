<?php

namespace Majora\Framework\Tests\Log;

use Majora\Framework\Log\LoggableTrait;
use Psr\Log\LoggerInterface;

/**
 * Class LoggableTraitTest.
 *
 * @see \Majora\Framework\Log\LoggableTrait
 */
class LoggableTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test LoggableTrait::registerLogger() method.
     */
    public function testRegisterLogger()
    {
        $logger = $this->createMock(LoggerInterface::class);

        $loggerTest = new LoggerTest();
        $loggerTest->registerLogger($logger, true);

        $loggerReflection = new \ReflectionProperty(LoggerTest::class, 'logger');
        $loggerReflection->setAccessible(true);

        $debugReflection = new \ReflectionProperty(LoggerTest::class, 'debug');
        $debugReflection->setAccessible(true);

        $this->assertEquals($logger, $loggerReflection->getValue($loggerTest));
        $this->assertTrue($debugReflection->getValue($loggerTest));
    }
}

/**
 * Class LoggerTest
 * Only used for our tests.
 */
class LoggerTest
{
    use LoggableTrait;
}
