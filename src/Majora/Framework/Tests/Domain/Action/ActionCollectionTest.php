<?php

namespace Majora\Framework\Tests\Domain\Action;

use Majora\Framework\Domain\Action\ActionCollection;
use Majora\Framework\Domain\Action\ActionInterface;

/**
 * Class ActionCollectionTest.
 *
 * @see \Majora\Framework\Domain\Action\ActionCollection
 */
class ActionCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test ActionCollection::resolve() method.
     */
    public function testResolve()
    {
        $actions = [];

        for ($i = 1; $i <= 3; $i++) {
            $action = $this->createMock(ActionInterface::class);

            $action
                ->expects($this->once())
                ->method('resolve')
                ->willReturn($i);

            $actions[$i] = $action;
        }

        $actionCollection = new ActionCollection($actions);

        $this->assertEquals(array_combine(array_keys($actions), array_keys($actions)), $actionCollection->resolve());
    }
}
