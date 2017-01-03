<?php

namespace Majora\Framework\Tests\Event;

use Majora\Framework\Event\BroadcastableEvent;

/**
 * Class CollectionableTraitTest.
 *
 * @see \Majora\Framework\Event\BroadcastableEvent
 */
class BroadcastableEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BroadcastableEvent
     */
    private $event;

    /**
     * Sets up.
     */
    protected function setUp()
    {
        $this->event = new BroadcastableEvent();
    }

    /**
     * Tears down.
     */
    protected function tearDown()
    {
        unset($this->event);
    }

    /**
     * Test BroadcastableEvent::setOriginName() method.
     */
    public function testOriginNameSetter()
    {
        $this->event->setOriginName('test');

        $originNameReflection = new \ReflectionProperty(BroadcastableEvent::class, 'originName');
        $originNameReflection->setAccessible(true);

        $this->assertEquals('test', $originNameReflection->getValue($this->event));
    }

    /**
     * Test BroadcastableEvent::getOriginName() method.
     */
    public function testOriginNameGetter()
    {
        $originNameReflection = new \ReflectionProperty(BroadcastableEvent::class, 'originName');
        $originNameReflection->setAccessible(true);
        $originNameReflection->setValue($this->event, 'test2');

        $this->assertEquals('test2', $this->event->getOriginName());
    }

    /**
     * Test BroadcastableEvent::getSubject() method.
     */
    public function testSubjectGetter()
    {
        $this->expectException(\BadMethodCallException::class);

        $this->event->getSubject();
    }

    /**
     * Test BroadcastableEvent::getAction() method.
     */
    public function testActionGetter()
    {
        $this->expectException(\BadMethodCallException::class);

        $this->event->getAction();
    }

    /**
     * Test BroadcastableEvent::setBroadcasted() method.
     */
    public function testBroadcastedSetter()
    {
        $isBroadcastedReflection = new \ReflectionProperty(BroadcastableEvent::class, 'isBroadcasted');
        $isBroadcastedReflection->setAccessible(true);

        $this->event->setBroadcasted(false);
        $this->assertFalse($isBroadcastedReflection->getValue($this->event));

        $this->event->setBroadcasted(true);
        $this->assertTrue($isBroadcastedReflection->getValue($this->event));
    }

    /**
     * Test BroadcastableEvent::isBroadcasted() method.
     */
    public function testIsBroadcasted()
    {
        $originNameReflection = new \ReflectionProperty(BroadcastableEvent::class, 'isBroadcasted');
        $originNameReflection->setAccessible(true);

        $originNameReflection->setValue($this->event, false);
        $this->assertFalse($this->event->isBroadcasted());

        $originNameReflection->setValue($this->event, true);
        $this->assertTrue($this->event->isBroadcasted());
    }
}
