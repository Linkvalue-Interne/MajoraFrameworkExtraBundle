<?php

namespace Majora\Framework\Tests\Api\Request;

use Majora\Framework\Api\Request\RestApiRequestFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class RestApiRequestFactoryTest.
 *
 * @see \Majora\Framework\Api\Request\RestApiRequestFactory
 */
class RestApiRequestFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var array
     */
    private $defaultOptions;

    /**
     * @var array
     */
    private $defaultBodyData;

    /**
     * @var array
     */
    private $routeMapping;

    /**
     * @var RestApiRequestFactory
     */
    private $restApiRequestFactory;

    /**
     * Sets up.
     */
    public function setUp()
    {
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->defaultOptions = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
        $this->defaultBodyData = [
            'param1' => 'value1',
            'param2' => 'value2',
        ];
        $this->routeMapping = [
            'cget' => 'my_api_route_for_cget',
            'get' => 'my_api_route_for_get',
            'post' => 'my_api_route_for_post',
            'put' => 'my_api_route_for_put',
            'delete' => 'my_api_route_for_delete',
        ];
        $this->restApiRequestFactory = new RestApiRequestFactory(
            $this->urlGenerator,
            $this->defaultOptions,
            $this->defaultBodyData,
            $this->routeMapping
        );
    }

    /**
     * Tears down.
     */
    public function tearDown()
    {
        unset(
            $this->urlGenerator,
            $this->defaultOptions,
            $this->defaultBodyData,
            $this->routeMapping,
            $this->restApiRequestFactory
        );
    }

    /**
     * Constructor.
     */
    public function testConstructor()
    {
        $urlGeneratorReflection = new \ReflectionProperty(RestApiRequestFactory::class, 'urlGenerator');
        $urlGeneratorReflection->setAccessible(true);

        $defaultOptionsReflection = new \ReflectionProperty(RestApiRequestFactory::class, 'defaultOptions');
        $defaultOptionsReflection->setAccessible(true);

        $defaultBodyDataReflection = new \ReflectionProperty(RestApiRequestFactory::class, 'defaultBodyData');
        $defaultBodyDataReflection->setAccessible(true);

        $routeMappingReflection = new \ReflectionProperty(RestApiRequestFactory::class, 'routeMapping');
        $routeMappingReflection->setAccessible(true);

        $this->assertEquals($this->urlGenerator, $urlGeneratorReflection->getValue($this->restApiRequestFactory));
        $this->assertEquals($this->defaultOptions, $defaultOptionsReflection->getValue($this->restApiRequestFactory));
        $this->assertEquals($this->defaultBodyData, $defaultBodyDataReflection->getValue($this->restApiRequestFactory));
        $this->assertEquals($this->routeMapping, $routeMappingReflection->getValue($this->restApiRequestFactory));
    }

    /**
     * Test that register route mapping set routeMapping property.
     */
    public function testRegisterRouteMapping()
    {
        $routeMapping = [
            'cget' => 'route_for_cget',
            'get' => 'route_for_get',
            'post' => 'route_for_post',
            'put' => 'route_for_put',
            'delete' => 'route_for_delete',
        ];

        $routeMappingReflection = new \ReflectionProperty(RestApiRequestFactory::class, 'routeMapping');
        $routeMappingReflection->setAccessible(true);

        $this->restApiRequestFactory->registerRouteMapping($routeMapping);

        $this->assertEquals($routeMapping, $routeMappingReflection->getValue($this->restApiRequestFactory));
    }

    /**
     * Test that RestApiRequestFactory::createRequestUri throw an exception if a preset does not exists.
     */
    public function testCreateRequestUriException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->restApiRequestFactory->createRequestUri('test');
    }

    /**
     * Test that RestApiRequestFactory::createRequestUri call generate method of UrlGeneratorInterface.
     */
    public function testCreateRequestUri()
    {
        $query = [
            'param1' => 'value1',
            'param2' => 'value2',
        ];

        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with('my_api_route_for_get', $query, UrlGeneratorInterface::ABSOLUTE_URL);

        $this->restApiRequestFactory->createRequestUri('get', $query);
    }

    /**
     * Test that RestApiRequestFactory::createRequestBodyData method override default body data.
     */
    public function testCreateRequestBodyData()
    {
        $body = [
            'param2' => 'test',
            'param3' => 'value3',
        ];

        $this->assertEquals(
            array_replace_recursive(
                $this->defaultBodyData,
                $body
            ),
            $this->restApiRequestFactory->createRequestBodyData($body)
        );
    }

    /**
     * Test that RestApiRequestFactory::createRequestOptions method override default options.
     */
    public function testCreateRequestOptions()
    {
        $options = [
            'Accept' => 'application/xml',
            'Accept-Language' => 'fr',
        ];

        $this->assertEquals(
            array_replace_recursive(
                $this->defaultOptions,
                $options
            ),
            $this->restApiRequestFactory->createRequestOptions($options)
        );
    }
}
