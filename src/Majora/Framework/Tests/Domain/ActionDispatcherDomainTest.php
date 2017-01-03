<?php

namespace Majora\Framework\Tests\Domain;

use Majora\Framework\Domain\Action\AbstractAction;
use Majora\Framework\Domain\ActionDispatcherDomain;
use Majora\Framework\Domain\Action\ActionFactory;

/**
 * Class ActionDispatcherDomainTest.
 *
 * @see \Majora\Framework\Domain\ActionDispatcherDomain
 */
class ActionDispatcherDomainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * @var ActionDispatcherDomain
     */
    private $actionDispatcherDomain;

    /**
     * Sets up.
     */
    public function setUp()
    {
        $this->actionFactory = $this->createMock(ActionFactory::class);
        $this->actionDispatcherDomain = new ActionDispatcherDomain($this->actionFactory);
    }

    /**
     * Tears down.
     */
    public function tearDown()
    {
        unset(
            $this->actionFactory,
            $this->actionDispatcherDomain
        );
    }

    /**
     * Test ActionDispatcherDomain::__constructor() method.
     */
    public function testConstructor()
    {
        $actionFactoryReflection = new \ReflectionProperty(ActionDispatcherDomain::class, 'actionFactory');
        $actionFactoryReflection->setAccessible(true);

        $this->assertEquals($this->actionFactory, $actionFactoryReflection->getValue($this->actionDispatcherDomain));
    }

    /**
     * Test ActionDispatcherDomain::getAction() method.
     */
    public function testActionGetter()
    {
        $name = 'my_name';
        $relatedEntity = 'my_related_entity';
        $arguments = [
            [
                'my_argument_1',
            ],
            'my_argument_2',
        ];

        $action = $this->createMock(AbstractAction::class);

        $this->actionFactory
            ->expects($this->once())
            ->method('createAction')
            ->with($name)
            ->willReturn($action);

        $action
            ->expects($this->once())
            ->method('denormalize')
            ->with($arguments[0])
            ->will($this->returnSelf());

        $action
            ->expects($this->once())
            ->method('init')
            ->with($relatedEntity, ...$arguments)
            ->will($this->returnSelf());

        $this->actionDispatcherDomain->getAction($name, $relatedEntity, $arguments[0], $arguments[1]);
    }

    /**
     * Test ActionDispatcherDomain::getAction() method without arguments.
     */
    public function testActionGetterWithoutArgument()
    {
        $name = 'my_name';
        $relatedEntity = 'my_related_entity';

        $action = $this->createMock(AbstractAction::class);

        $this->actionFactory
            ->expects($this->once())
            ->method('createAction')
            ->with($name)
            ->willReturn($action);

        $action
            ->expects($this->once())
            ->method('denormalize')
            ->with([])
            ->will($this->returnSelf());

        $action
            ->expects($this->once())
            ->method('init')
            ->with($relatedEntity)
            ->will($this->returnSelf());

        $this->actionDispatcherDomain->getAction($name, $relatedEntity);
    }
}
