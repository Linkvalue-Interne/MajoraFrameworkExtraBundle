<?php

namespace Majora\Framework\Tests\Domain\Action\Dal;

use Majora\Framework\Domain\Action\Dal\DalActionTrait;
use Majora\Framework\Validation\ValidationException;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class DalActionTraitTest.
 *
 * @see \Majora\Framework\Domain\Action\Dal\DalActionTrait
 */
class DalActionTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test DalActionTraitTest::setEventDispatcher method.
     */
    public function testEventDispatcherSetter()
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $dalActionTest = new DalActionTest();
        $dalActionTest->setEventDispatcher($eventDispatcher);

        $eventDispatcherReflection = new \ReflectionProperty(DalActionTest::class, 'eventDispatcher');
        $eventDispatcherReflection->setAccessible(true);

        $this->assertEquals($eventDispatcher, $eventDispatcherReflection->getValue($dalActionTest));
    }

    /**
     * Test DalActionTraitTest::setEventDispatcher method.
     */
    public function testValidatorSetter()
    {
        $validator = $this->createMock(ValidatorInterface::class);

        $dalActionTest = new DalActionTest();
        $dalActionTest->setValidator($validator);

        $validatorReflection = new \ReflectionProperty(DalActionTest::class, 'validator');
        $validatorReflection->setAccessible(true);

        $this->assertEquals($validator, $validatorReflection->getValue($dalActionTest));
    }

    /**
     * Test DalActionTraitTest::assertEntityIsValid method with a scope
     */
    public function testAssertEntityIsValidWithScope()
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $entity = new ObjectTest();
        $scope = 'test';

        $dalActionTest = new DalActionTest();
        $dalActionTest->setValidator($validator);

        $validator
            ->expects($this->once())
            ->method('validate')
            ->with($entity, null, [$scope])
            ->willReturn([]);

        $assertEntityIsValidReflection = new \ReflectionMethod(DalActionTest::class, 'assertEntityIsValid');
        $assertEntityIsValidReflection->setAccessible(true);

        $this->assertNull($assertEntityIsValidReflection->invokeArgs($dalActionTest, [$entity, $scope]));
    }

    /**
     * Test DalActionTraitTest::assertEntityIsValid method without scope.
     */
    public function testAssertEntityIsValidWithoutScope()
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $entity = new ObjectTest();

        $dalActionTest = new DalActionTest();
        $dalActionTest->setValidator($validator);

        $validator
            ->expects($this->once())
            ->method('validate')
            ->with($entity, null, null)
            ->willReturn([]);

        $assertEntityIsValidReflection = new \ReflectionMethod(DalActionTest::class, 'assertEntityIsValid');
        $assertEntityIsValidReflection->setAccessible(true);

        $this->assertNull($assertEntityIsValidReflection->invokeArgs($dalActionTest, [$entity]));
    }

    /**
     * Test DalActionTraitTest::assertEntityIsValid method if there is errors.
     */
    public function testAssertEntityIsValidErrorException()
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $entity = new ObjectTest();
        $constraintList = new ConstraintViolationList();
        $constraintList->add($this->createMock(ConstraintViolationInterface::class));

        $dalActionTest = new DalActionTest();
        $dalActionTest->setValidator($validator);

        $validator
            ->expects($this->once())
            ->method('validate')
            ->with($entity, null, null)
            ->willReturn($constraintList);

        $this->expectException(ValidationException::class);

        $assertEntityIsValidReflection = new \ReflectionMethod(DalActionTest::class, 'assertEntityIsValid');
        $assertEntityIsValidReflection->setAccessible(true);
        $assertEntityIsValidReflection->invokeArgs($dalActionTest, [$entity]);
    }

    /**
     * Test DalActionTraitTest::assertEntityIsValid method if the validator is not configured.
     */
    public function testAssertEntityIsValidException()
    {
        $this->expectException(\BadMethodCallException::class);

        $assertEntityIsValidReflection = new \ReflectionMethod(DalActionTest::class, 'assertEntityIsValid');
        $assertEntityIsValidReflection->setAccessible(true);
        $assertEntityIsValidReflection->invokeArgs(new DalActionTest(), ['test']);
    }

    /**
     * Test DalActionTraitTest::fireEvent method.
     */
    public function testFireEvent()
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $event = $this->createMock(Event::class);
        $name = 'eventName';

        $dalActionTest = new DalActionTest();
        $dalActionTest->setEventDispatcher($eventDispatcher);

        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($name, $event);

        $fireEventReflection = new \ReflectionMethod(DalActionTest::class, 'fireEvent');
        $fireEventReflection->setAccessible(true);
        $fireEventReflection->invokeArgs($dalActionTest, [$name, $event]);
    }

    /**
     * Test DalActionTraitTest::fireEvent method throw exception if the event dispatcher is not configured.
     */
    public function testFireEventException()
    {
        $this->expectException(\BadMethodCallException::class);

        $fireEventReflection = new \ReflectionMethod(DalActionTest::class, 'fireEvent');
        $fireEventReflection->setAccessible(true);
        $fireEventReflection->invokeArgs(
            new DalActionTest(),
            [
                'test',
                $this->createMock(Event::class),
            ]
        );
    }

    /**
     * Test DalActionTraitTest::getScopes method.
     */
    public function testScopesGetter()
    {
        $this->assertEquals([], DalActionTest::getScopes());
    }

    /**
     * Test DalActionTraitTest::normalize method.
     */
    public function testNormalize()
    {
        $dalActionTest = new DalActionTest();

        $this->assertEquals([], $dalActionTest->normalize());
        $this->assertEquals([], $dalActionTest->normalize('test'));
    }

    /**
     * Test DalActionTraitTest::denormalize method.
     */
    public function testDenormalize()
    {
        $dalActionTest = new DalActionTest();

        $this->assertEquals($dalActionTest, $dalActionTest->denormalize([]));
        $this->assertEquals($dalActionTest, $dalActionTest->denormalize(['test']));
    }
}

/**
 * Class DalActionTest.
 * Used only to test the trait.
 */
class DalActionTest
{
    use DalActionTrait;
}

/**
 * Class ObjectTest
 * Used only to test the trait.
 */
class ObjectTest
{
}
