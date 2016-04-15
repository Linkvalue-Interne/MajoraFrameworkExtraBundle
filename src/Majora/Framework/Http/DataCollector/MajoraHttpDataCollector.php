<?php

namespace  Majora\Framework\Http\DataCollector;

use Majora\Framework\Http\Event\HttpRequestEvent;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class MajoraHttpDataCollector
 * @package Majora\HttpBundle\DataCollector
 */
class MajoraHttpDataCollector extends DataCollector
{

    /**
     * MajoraHttpDataCollector constructor.
     */
    public function __construct()
    {
        $this->data['majoraHttp'] = [
            'commands' => new \SplQueue(),
        ];
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param \Exception|null $exception
     */
    function collect(Request $request, Response $response, \Exception $exception = null)
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return "majorahttp";
    }

    /**
     * @param MajoraHttpEvent $event
     */
    public function onRequest(HttpRequestEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();
        $body = (array) json_decode($response->getBody());

        $data = array(
            'responseBody' => $body,
            'uri' => $request->getUri(),
            'method' => $request->getMethod(),
            'headers' => $request->getHeaders(),
            'statusCode' => $response->getStatusCode(),
            'reasonPhrase' => $response->getReasonPhrase(),
            'executionTime' => $event->getExecutionTime(),
        );

        $this->data['majoraHttp']['commands']->enqueue($data);
    }

    public function getCommands()
    {
        return $this->data['majoraHttp']['commands'];
    }

}