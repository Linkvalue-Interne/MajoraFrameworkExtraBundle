<?php

namespace Majora\Framework\Http\Event;

use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\EventDispatcher\Event;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpRequestEvent extends Event
{
    const EVENT_NAME = 'majora_http.event';

    /**
     * Command execution time
     *
     * @var float
     */
    protected $executionTime;
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var Response
     */
    protected $response;

    /**
     * @var mixed
     */
    protected $reason;

    /**
     * @var string
     */
    protected $clientId;

    public function __construct(RequestInterface $request, $clientId)
    {
        $this->request= $request;
        $this->clientId = $clientId;
    }

    /**
     * Return request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
    /**
     * Set Response
     *
     * @param Response $response
     *
     * @return $this
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
        $this->reason = $this->response->getReasonPhrase();
        return $this;
    }
    /**
     * Return response
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }


    /**
     * @return float
     */
    public function getExecutionTime()
    {
        return $this->executionTime;
    }


    public function setExecutionTime($time)
    {
        $this->executionTime = $time;
    }
}