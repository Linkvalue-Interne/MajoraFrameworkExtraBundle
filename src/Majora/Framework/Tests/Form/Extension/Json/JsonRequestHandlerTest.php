<?php

namespace Majora\Framework\Tests\Form\Extension\Json;

use Majora\Framework\Form\Extension\Json\JsonRequestHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class JsonRequestHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JsonRequestHandler
     */
    private $jsonRequestHandler;

    /**
     * @var HttpFoundationRequestHandler
     */
    private $httpFoundationRequestHandler;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var Request
     */
    private $request;

    public function setUp()
    {
        $this->httpFoundationRequestHandler = \Phake::mock(HttpFoundationRequestHandler::class);
        $this->jsonRequestHandler = new JsonRequestHandler($this->httpFoundationRequestHandler);
        $this->form = \Phake::mock(FormInterface::class);
        $this->request = \Phake::mock(Request::class);
    }

    public function testRequestWithJsonContentShouldSubmitJson()
    {
        \Phake::when($this->request)
            ->getContentType()
            ->thenReturn('json');
        \Phake::when($this->request)
            ->getContent()
            ->thenReturn('{ "json": "test" }');

        $this->jsonRequestHandler->handleRequest($this->form, $this->request);

        \Phake::verify($this->form, \Phake::times(1))->submit('{ "json": "test" }');
    }

    public function testRequestWithoutJsonContentShouldSubmitProxy()
    {
        \Phake::when($this->request)
            ->getContentType()
            ->thenReturn('txt');

        $this->jsonRequestHandler->handleRequest($this->form, $this->request);

        \Phake::verify($this->httpFoundationRequestHandler, \Phake::times(1))->handleRequest(
            $this->form,
            $this->request
        );
    }

    public function testInvalidRequestTypeShouldThrowException()
    {
        $this->setExpectedException(UnexpectedTypeException::class);

        $this->jsonRequestHandler->handleRequest($this->form, ['request']);
    }
}
