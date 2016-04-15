<?php

namespace Majora\Framework\Http\Middleware;


use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\PrepareBodyMiddleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Majora\Framework\Http\Event\HttpRequestEvent;
use Symfony\Component\Stopwatch\Stopwatch;

class MajoraEventDispatcher
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;
    /**
     * @var array
     */
    protected $event;
    /**
     * @var string
     */
    protected $clientId;

    /**
     * MajoraEventDispatcher constructor.
     * @param $stopWatch
     * @param EventDispatcherInterface $eventDispatcher
     * @param $clientId
     */
    public function __construct(Stopwatch $stopWatch, EventDispatcherInterface $eventDispatcher, $clientId)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->clientId = $clientId;
        $this->stopWatch = $stopWatch;
    }

    /**
     * @param HandlerStack $stack
     * @return HandlerStack
     */
    public function push(HandlerStack $stack)
    {
        $stack->push(Middleware::mapRequest(function (RequestInterface $request) {
            $this->initEvent($request);
            return $request;
        }));


        $stack->push(function (callable $handler) {
            return function (
                RequestInterface $request,
                array $options
            ) use ($handler) {

                $promise = $handler($request, $options);

                return $promise->then(
                    function (ResponseInterface $response) use ($request) {

                        $this->dispatchEvent($response);
                        return $response;
                    },
                    function (\Exception $reason) use ($request) {
                        // impossible to pass \exception to dispatch event
                        dump($reason);
                        $this->dispatchEvent($reason);
                        throw $reason;
                    }
                );
            };
        });
        return $stack;
    }

    /**
     * @param RequestInterface $request
     */
    protected function initEvent(RequestInterface $request)
    {
        $this->stopWatch->start('majoraEvent.'.$this->clientId);
        $event = new HttpRequestEvent($request, $this->clientId);
        $this->event = $event;
    }

    /**
     * @param ResponseInterface $response
     */
    protected function dispatchEvent(ResponseInterface $response)
    {
        $this->event->setResponse($response);
        $this->stopWatch->stop('majoraEvent.'.$this->clientId);
        $this->event->setExecutionTime($this->stopWatch->getEvent('majoraEvent.'.$this->clientId)->getDuration());
        $this->eventDispatcher->dispatch(HttpRequestEvent::EVENT_NAME, $this->event);
    }
}