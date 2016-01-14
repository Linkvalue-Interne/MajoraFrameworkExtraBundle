<?php

namespace Majora\Framework\Form\Extension\Json\Tests\Type;

use Majora\Framework\Form\Extension\Json\Type\FormTypeJsonExtension;
use Majora\Framework\Form\Extension\Json\JsonExtensionListener;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\RequestHandlerInterface;

class FormTypeJsonExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormTypeJsonExtension
     */
    private $formTypeJsonExtension;

    /**
     * @var RequestHandlerInterface
     */
    private $requestHandler;

    /**
     * @var FormBuilderInterface
     */
    private $builder;

    public function setUp()
    {
        $this->requestHandler = \Phake::mock(RequestHandlerInterface::class);
        $this->formTypeJsonExtension = new FormTypeJsonExtension($this->requestHandler);
        $this->builder = \Phake::mock(FormBuilderInterface::class);
    }

    public function testShouldBindHandlerAndEventListenerWhenJsonFormatOptionIsTrue()
    {
        $this->buildForm(true);

        \Phake::verify($this->builder, \Phake::times(1))->setRequestHandler($this->requestHandler);
        \Phake::verify($this->builder, \Phake::times(1))->addEventSubscriber(new JsonExtensionListener());
    }

    public function testShouldBindHandlerAndNotBindEventListenerWhenJsonFormatOptionIsFalse()
    {
        $this->buildForm(false);

        \Phake::verify($this->builder, \Phake::times(1))->setRequestHandler($this->requestHandler);
        \Phake::verify($this->builder, \Phake::never())->addEventSubscriber(new JsonExtensionListener());
    }

    /**
     * @param bool $jsonFormat
     */
    private function buildForm($jsonFormat)
    {
        $this->formTypeJsonExtension->buildForm($this->builder, ['json_format' => $jsonFormat]);
    }
}
