<?php

namespace Majora\Bundle\FrameworkExtraBundle\Tests\Functional;

class JsonFormExtensionTest extends WebTestCase
{
    private $client;

    public function setUp()
    {
        $this->client = $this->createClient(['test_case' => 'JsonFormExtension']);
    }

    public function testJsonRequest()
    {
        $json = '{ "name": "test1" }';
        $this->client->request(
            'POST',
            '/json-form-extension/json',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $json
        );

        $expectedJson = json_encode([
            'Data' => ['name' => 'test1', 'lastname' => null],
            'NormData' => ['name' => 'test1', 'lastname' => null],
            'ViewData' => ['name' => 'test1', 'lastname' => null],
        ], 15);
        $this->assertEquals(
            $expectedJson,
            $this->client->getResponse()->getContent()
        );
    }

    public function testJsonRequestInvalidJsonError()
    {
        $json = '{ "name" "test1" }';
        $this->client->request(
            'POST',
            '/json-form-extension/json',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $json
        );

        $expectedJson = json_encode([
            'Class' => 'InvalidArgumentException',
            'Message' => "Invalid submitted json data, error 4 : Syntax error, json : $json",
        ], 15);
        $this->assertEquals(
            $expectedJson,
            $this->client->getResponse()->getContent()
        );
    }

    public function testJsonRequestNotAStringError()
    {
        $json = ['test' => 'test'];
        $this->client->request(
            'POST',
            '/json-form-extension/json',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $json
        );

        $expectedJson = json_encode([
            'Class' => 'InvalidArgumentException',
            'Message' => 'Invalid argument: the submitted variable must be a string when you enable the json_format option.',
        ], 15);
        $this->assertEquals(
            $expectedJson,
            $this->client->getResponse()->getContent()
        );
    }

    public function testPostRequest()
    {
        $this->client->request(
            'POST',
            '/json-form-extension/post',
            ['form' => ['name' => 'test1']]
        );
        $expectedJson = json_encode([
            'Data' => ['name' => 'test1', 'lastname' => null],
            'NormData' => ['name' => 'test1', 'lastname' => null],
            'ViewData' => ['name' => 'test1', 'lastname' => null],
        ], 15);
        $this->assertEquals(
            $expectedJson,
            $this->client->getResponse()->getContent()
        );
    }
}
