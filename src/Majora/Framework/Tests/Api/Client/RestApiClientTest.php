<?php

namespace Majora\Framework\Tests\Api\Client;

use GuzzleHttp\ClientInterface;
use Majora\Framework\Api\Client\RestApiClient;
use Majora\Framework\Api\Request\RestApiRequestFactory;

/**
 * Class RestApiClientTest.
 *
 * @see \Majora\Framework\Api\Client\RestApiClient
 */
class RestApiClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var RestApiRequestFactory
     */
    private $requestFactory;

    /**
     * @var RestApiClient
     */
    private $restApiClient;

    /**
     * Sets up.
     */
    public function setUp()
    {
        $this->httpClient = $this->createMock(ClientInterface::class);
        $this->requestFactory = $this->createMock(RestApiRequestFactory::class);
        $this->restApiClient = new RestApiClient(
            $this->httpClient,
            $this->requestFactory
        );
    }

    /**
     * Tears down.
     */
    public function tearDown()
    {
        unset(
            $this->httpClient,
            $this->requestFactory,
            $this->restApiClient
        );
    }

    /**
     * Test that constructor set up properties.
     */
    public function testConstructor()
    {
        $httpClientReflection = new \ReflectionProperty(RestApiClient::class, 'httpClient');
        $httpClientReflection->setAccessible(true);

        $requestFactoryReflection = new \ReflectionProperty(RestApiClient::class, 'requestFactory');
        $requestFactoryReflection->setAccessible(true);

        $this->assertEquals($this->httpClient, $httpClientReflection->getValue($this->restApiClient));
        $this->assertEquals($this->requestFactory, $requestFactoryReflection->getValue($this->restApiClient));
    }

    /**
     * Test RestApiClient::send()
     */
    public function testSend()
    {
        $name = 'my_name';
        $method = 'my_method';

        $query = [
            'param1' => 'test1',
            'param2' => 'test2',
        ];

        $options = [
            'option1' => 'test1',
            'option2' => 'test2',
        ];

        $body = [
            'field1' => 'value1',
        ];

        $request_uri = 'http://majora.local?param1=test1&param2=test2';

        $this->requestFactory
            ->expects($this->once())
            ->method('createRequestUri')
            ->with($name, $query)
            ->willReturn($request_uri);

        $this->requestFactory
            ->expects($this->once())
            ->method('createRequestOptions')
            ->with($options)
            ->willReturn($options);

        $this->requestFactory
            ->expects($this->once())
            ->method('createRequestBodyData')
            ->with($body)
            ->willReturn($body);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with($method, $request_uri, array_replace_recursive($options, ['json' => $body]));

        $this->restApiClient->send($name, $method, $query, $body, $options);
    }

    /**
     * Test that cget method call the send method and arguments.
     */
    public function testCget()
    {
        $query = [
            'param1' => 'test1',
            'param2' => 'test2',
        ];

        $options = [
            'option1' => 'test1',
            'option2' => 'test2',
        ];

        $body = [];

        $restApiClient = $this->getMockBuilder(RestApiClient::class)
            ->setMethods(['send'])
            ->disableOriginalConstructor()
            ->getMock();

        $restApiClient
            ->expects($this->once())
            ->method('send')
            ->with('cget', 'GET', $query, $body, $options);

        $restApiClient->cget($query, $options);
    }

    /**
     * Test that get method call the send method and arguments.
     */
    public function testGet()
    {
        $query = [
            'param1' => 'test1',
            'param2' => 'test2',
        ];

        $options = [
            'option1' => 'test1',
            'option2' => 'test2',
        ];

        $body = [];

        $restApiClient = $this->getMockBuilder(RestApiClient::class)
            ->setMethods(['send'])
            ->disableOriginalConstructor()
            ->getMock();

        $restApiClient
            ->expects($this->once())
            ->method('send')
            ->with('get', 'GET', $query, $body, $options);

        $restApiClient->get($query, $options);
    }

    /**
     * Test that post method call the send method and arguments.
     */
    public function testPost()
    {
        $query = [
            'param1' => 'test1',
            'param2' => 'test2',
        ];

        $options = [
            'option1' => 'test1',
            'option2' => 'test2',
        ];

        $body = [
            'field1' => 'value1',
        ];

        $restApiClient = $this->getMockBuilder(RestApiClient::class)
            ->setMethods(['send'])
            ->disableOriginalConstructor()
            ->getMock();

        $restApiClient
            ->expects($this->once())
            ->method('send')
            ->with('post', 'POST', $query, $body, $options);

        $restApiClient->post($query, $body, $options);
    }

    /**
     * Test that put method call the send method and arguments.
     */
    public function testPut()
    {
        $query = [
            'param1' => 'test1',
            'param2' => 'test2',
        ];

        $options = [
            'option1' => 'test1',
            'option2' => 'test2',
        ];

        $body = [
            'field1' => 'value1',
        ];

        $restApiClient = $this->getMockBuilder(RestApiClient::class)
            ->setMethods(['send'])
            ->disableOriginalConstructor()
            ->getMock();

        $restApiClient
            ->expects($this->once())
            ->method('send')
            ->with('put', 'PUT', $query, $body, $options);

        $restApiClient->put($query, $body, $options);
    }

    /**
     * Test that delete method call the send method and arguments.
     */
    public function testDelete()
    {
        $query = [
            'param1' => 'test1',
            'param2' => 'test2',
        ];

        $options = [
            'option1' => 'test1',
            'option2' => 'test2',
        ];

        $body = [
            'field1' => 'value1',
        ];

        $restApiClient = $this->getMockBuilder(RestApiClient::class)
            ->setMethods(['send'])
            ->disableOriginalConstructor()
            ->getMock();

        $restApiClient
            ->expects($this->once())
            ->method('send')
            ->with('delete', 'DELETE', $query, $body, $options);

        $restApiClient->delete($query, $body, $options);
    }
}
