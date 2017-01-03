<?php

namespace Majora\Framework\Tests\Domain\Action;

use Majora\Framework\Domain\Action\ActionFactory;
use Majora\Framework\Domain\Action\ActionInterface;

/**
 * Class ActionFactoryTest.
 *
 * @see \Majora\Framework\Domain\Action\ActionFactory
 */
class ActionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $actions;

    /**
     * Sets up.
     */
    public function setUp()
    {
        $this->actions = [
            'create' => $this->createMock(ActionInterface::class),
            'read' => $this->createMock(ActionInterface::class),
            'update' => $this->createMock(ActionInterface::class),
            'delete' => $this->createMock(ActionInterface::class),
        ];
    }

    /**
     * Tears down.
     */
    public function tearDown()
    {
        unset(
            $this->actions
        );
    }

    /**
     * Test ActionFactory::_construct() method.
     */
    public function testConstructor()
    {
        $actionFactory = new ActionFactory($this->actions);

        $actionsReflection = new \ReflectionProperty(ActionFactory::class, 'actions');
        $actionsReflection->setAccessible(true);

        $collection = $actionsReflection->getValue($actionFactory);

        foreach ($this->actions as $name => $action) {
            $this->assertEquals($action, $collection->get($name));
        }
    }

    /**
     * Test ActionFactory::registerAction() method.
     */
    public function testRegisterAction()
    {
        $actionFactory = new ActionFactory();

        foreach ($this->actions as $name => $action) {
            $actionFactory->registerAction($name, $action);
        }

        $actionsReflection = new \ReflectionProperty(ActionFactory::class, 'actions');
        $actionsReflection->setAccessible(true);

        $collection = $actionsReflection->getValue($actionFactory);

        foreach ($this->actions as $name => $action) {
            $this->assertEquals($action, $collection->get($name));
        }
    }

    /**
     * Test ActionFactory::createAction() method.
     */
    public function testCreateAction()
    {
        $actionFactory = new ActionFactory($this->actions);

        foreach ($this->actions as $name => $action) {
            $this->assertEquals($this->actions[$name], $actionFactory->createAction($name));
        }
    }

    /**
     * Test ActionFactory::createAction() method throw an exception if the action does not exists.
     */
    public function testCreateActionException()
    {
        $actionFactory = new ActionFactory($this->actions);

        $this->expectException(\InvalidArgumentException::class);

        $actionFactory->createAction('test');
    }
}
