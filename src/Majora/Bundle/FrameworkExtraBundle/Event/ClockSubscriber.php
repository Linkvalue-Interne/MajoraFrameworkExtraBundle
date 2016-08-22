<?php

namespace Majora\Bundle\FrameworkExtraBundle\Event;

use Majora\Framework\Date\Clock;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Framework event subscriber, looking for a mocked date
 * If guessed, define it as current one for Clock service
 *
 * @example
 *      /app_dev.php/article/1?_date_mock=2015-01-01
 */
class ClockSubscriber extends Clock implements EventSubscriberInterface
{
    protected $mockParamName;

    /**
     * construct
     *
     * @param string $mockParamName
     */
    public function __construct($mockParamName)
    {
        $this->mockParamName = $mockParamName;
    }

    /**
     * @see EventSubscriberInterface::getSubscribedEvents()
     */
    static public function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', 100),
            ConsoleEvents::COMMAND => array('onConsoleCommand', 100),
        );
    }

    /**
     * kernel request event handler
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$strMockedDate = $request->query->get($this->mockParamName)) {
            return;
        }

        $this->mock($strMockedDate);
    }

    /**
     * console command event handler
     */
    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
        $input = $event->getInput();
        if (!$input->hasOption($this->mockParamName)) {
            return;
        }

        $this->mock($input->getOption($this->mockParamName));
    }
}
