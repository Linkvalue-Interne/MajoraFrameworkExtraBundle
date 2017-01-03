<?php

namespace Majora\Framework\Tests\Domain\Action;

use Majora\Framework\Domain\Action\AbstractAction;

/**
 * Class AbstractActionTest.
 *
 * @see \Majora\Framework\Domain\Action\AbstractAction
 */
class AbstractActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test AbstractAction::init() method.
     */
    public function testInit()
    {
        $abstractionAction = $this
            ->getMockBuilder(AbstractAction::class)
            ->setMethodsExcept(['init'])
            ->getMock();

        $this->assertEquals($abstractionAction, $abstractionAction->init());
    }
}
