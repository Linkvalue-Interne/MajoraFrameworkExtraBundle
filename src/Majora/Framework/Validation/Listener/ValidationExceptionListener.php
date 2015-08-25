<?php

namespace Majora\Framework\Validation\Listener;

use Majora\Framework\Validation\ValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Event listener for ValidationExceptions, for formating and response code handleling
 */
class ValidationExceptionListener implements EventSubscriberInterface
{
    protected static $supported = array(
        'application/json',
        'application/x-json',
        'text/json',
        'json'
    );

    protected $debug;

    /**
     * construct
     *
     * @param boolean $debug
     */
    public function __construct($debug)
    {
        $this->debug = $debug;
    }

    /**
     * @see EventSubscriberInterface::getSubscribedEvents()
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => array('onKernelException'),
        );
    }

    /**
     * Exception event handler
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $request   = $event->getRequest();
        $exception = $event->getException();

        if (!$this->supports($request)) {
            return;
        }

        $statusCode = $exception instanceof HttpException ? $exception->getStatusCode() : 500;
        $errors     = array();

        switch(true) {

            // validation exception
            case $exception instanceof ValidationException :
                $errors = $this->parseValidationException($exception);
                $statusCode = 400;
            break;

            // other http exceptions
            default:
                $error = array($exception->getMessage());
        }

        $data = array(
            'code'    => $statusCode,
            'message' => $exception->getMessage(),
            'errors'  => $errors
        );
        if ($this->debug) {
            $data['trace'] = explode("\n", $exception->getTraceAsString());
        }

        $event->setResponse(new JsonResponse($data, $statusCode));
    }

    protected function parseValidationException(ValidationException $exception)
    {
        $errors = array();

        foreach ($exception->getReport() as $key => $violation) {
            if (is_string($violation)) {
                $errors[$key] = $violation;

                continue;
            }

            $violation = $violation instanceof FormError ?
                $violation->getCause() :
                $violation
            ;

            $errors[(string) $violation->getPropertyPath()] = $violation->getMessage();
        }

        return $errors;
    }

    protected function supports(Request $request)
    {
        return in_array($request->get('_format'), self::$supported);
    }
}
