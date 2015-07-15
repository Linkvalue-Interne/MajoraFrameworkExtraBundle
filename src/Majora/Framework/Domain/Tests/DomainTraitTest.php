<?php

namespace Majora\Framework\Domain\Tests;

use Majora\Framework\Domain\DomainTrait;
use PHPUnit_Framework_TestCase;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Unit test class for DomainTrait.php.
 *
 * @see Majora\Framework\Domain\DomainTrait
 */
class DomainTraitTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * tests event trigger method without any event dispatcher defined.
     *
     * @expectedException              BadMethodCallException
     * @expectedExceptionMessageRegExp #Method .+() cannot be used while event dispatcher isnt configured.#
     */
    public function testEventsWithoutDispatcher()
    {
        $domain = new Domain();

        $domain->publicFireEvent('joffrey_sick_bastard', new Event());
    }

    /**
     * test fireEvent() method.
     */
    public function testFireEvent()
    {
        $eventDispatcher = $this->prophesize('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $eventDispatcher->dispatch('joffrey_sick_bastard', new Event())
            ->shouldBeCalled()
        ;

        $domain = new Domain();
        $domain->setEventDispatcher($eventDispatcher->reveal());

        $domain->publicFireEvent('joffrey_sick_bastard', new Event());
    }

    /**
     * tests validation method without any validator defined.
     *
     * @expectedException              BadMethodCallException
     * @expectedExceptionMessageRegExp #Method .+() cannot be used while validator isnt configured.#
     */
    public function testValidationWithoutValidator()
    {
        $domain = new Domain();

        $domain->publicAssertEntityIsValid('Joffrey', 'is the real king');
    }

    /**
     * tests validation ok cases.
     *
     * @dataProvider testValidationProvider
     */
    public function testValidationOk($entity, $scope, $expectedScope)
    {
        $object = new \StdClass();
        $object->name = $entity;

        $validator = $this->prophesize('Symfony\Component\Validator\Validator\ValidatorInterface');
        $validator->validate($object, Argument::exact(null), $expectedScope)
            ->willReturn(array())
            ->shouldBeCalled()
        ;

        $domain = new Domain();
        $domain->setValidator($validator->reveal());

        $domain->publicAssertEntityIsValid($object, $scope);
    }

    /**
     * tests validation ko cases.
     *
     * @dataProvider testValidationProvider
     *
     * @expectedException              Majora\Framework\Validation\ValidationException
     * @expectedExceptionMessageRegExp #Validation failed on .*#
     */
    public function testValidationKo($entity, $scope, $expectedScope)
    {
        $object = $this->prophesize('Majora\Framework\Model\CollectionableInterface');
        $object->getId()->willReturn($entity)->shouldBeCalled();
        $object = $object->reveal();

        $violation1    = $this->prophesize('Symfony\Component\Validator\ConstraintViolationInterface')->reveal();
        $violation2    = $this->prophesize('Symfony\Component\Validator\ConstraintViolationInterface')->reveal();
        $violationList = new ConstraintViolationList(array($violation1, $violation2));

        $validator = $this->prophesize('Symfony\Component\Validator\Validator\ValidatorInterface');
        $validator->validate($object, Argument::exact(null), $expectedScope)
            ->willReturn($violationList)
            ->shouldBeCalled()
        ;

        $domain = new Domain();
        $domain->setValidator($validator->reveal());

        $domain->publicAssertEntityIsValid($object, $scope);
    }

    public function testValidationProvider()
    {
        return array(
            array('Daenerys', null, null),
            array('Daenerys', 'has dragons', array('has dragons')),
            array('Daenerys', array('Drogon', 'Rhaegal', 'Viserion'), array('Drogon', 'Rhaegal', 'Viserion')),
        );
    }
}

class Domain
{
    use DomainTrait;

    public function publicAssertEntityIsValid($entity, $scope = null)
    {
        return $this->assertEntityIsValid($entity, $scope);
    }
    public function publicFireEvent($eventName, Event $event)
    {
        return $this->fireEvent($eventName, $event);
    }
}
