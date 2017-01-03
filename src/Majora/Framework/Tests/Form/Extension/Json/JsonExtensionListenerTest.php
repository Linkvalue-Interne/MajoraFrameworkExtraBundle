<?php

namespace Majora\Framework\Tests\Form\Extension\Json;

use Majora\Framework\Form\Extension\Json\JsonExtensionListener;
use Symfony\Component\Form\FormEvent;

class JsonExtensionListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JsonExtensionListener
     */
    private $jsonExtensionListener;

    /**
     * @var FormEvent
     */
    private $formEvent;

    public function setUp()
    {
        $this->jsonExtensionListener = new JsonExtensionListener();
        $this->formEvent = \Phake::mock(FormEvent::class);
    }

    /**
     * @dataProvider jsonProvider
     */
    public function testValidJsonShouldBeDecoded($json, $data)
    {
        \Phake::when($this->formEvent)
            ->getData()
            ->thenReturn($json);

        $this->jsonExtensionListener->onPreSubmit($this->formEvent);

        \Phake::verify($this->formEvent, \Phake::times(1))->setData($data);
    }

    public function testInvalidJsonShouldThrowInvalidArgumentExceptionError()
    {
        $this->setExpectedExceptionRegExp(
            \InvalidArgumentException::class,
            '/^Invalid submitted json data, error (.*) : (.*), json : invalid json$/'
        );
        $json = 'invalid json';

        \Phake::when($this->formEvent)
            ->getData()
            ->thenReturn($json);

        $this->jsonExtensionListener->onPreSubmit($this->formEvent);
    }

    public function testEventWithoutStringShouldThrowInvalidArgumentExceptionError()
    {
        $this->setExpectedExceptionRegExp(
            \InvalidArgumentException::class,
            '/^Invalid argument: the submitted variable must be a string when you enable the json_format option.$/'
        );
        $json = ['invalid json'];

        \Phake::when($this->formEvent)
            ->getData()
            ->thenReturn($json);

        $this->jsonExtensionListener->onPreSubmit($this->formEvent);
    }

    public function jsonProvider()
    {
        return [
            [
                '{ "name": "test" }',
                ['name' => 'test'],
            ],
            [
                '{ "name": "Robert", "lastname": "Michel", "parent": { "name": "Michel", "lastname": "Robert" } }',
                [
                    'name' => 'Robert',
                    'lastname' => 'Michel',
                    'parent' => [
                        'name' => 'Michel',
                        'lastname' => 'Robert',
                    ],
                ],
            ],
        ];
    }
}
