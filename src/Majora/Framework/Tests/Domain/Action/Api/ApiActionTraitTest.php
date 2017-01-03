<?php

namespace Majora\Framework\Tests\Domain\Action\Api;

use Majora\Framework\Domain\Action\Api\ApiActionTrait;
use Majora\Framework\Api\Client\RestApiClient;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class ApiActionTraitTest.
 *
 * @see \Majora\Framework\Domain\Action\Api\ApiActionTrait
 */
class ApiActionTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test ApiActionTrait::setRestApiClient() method.
     */
    public function testRestApiClientSetter()
    {
        $restApiClient = $this->createMock(RestApiClient::class);

        $apiActionTest = new ApiActionTest();
        $apiActionTest->setRestApiClient($restApiClient);

        $restApiClientReflection = new \ReflectionProperty(ApiActionTest::class, 'restClient');
        $restApiClientReflection->setAccessible(true);

        $this->assertEquals($restApiClient, $restApiClientReflection->getValue($apiActionTest));
    }

    /**
     * Test ApiActionTrait::getRestApiClient() method.
     */
    public function testRestApiClientGetter()
    {
        $restApiClient = $this->createMock(RestApiClient::class);

        $apiActionTest = new ApiActionTest();

        $restApiClientReflection = new \ReflectionProperty(ApiActionTest::class, 'restClient');
        $restApiClientReflection->setAccessible(true);
        $restApiClientReflection->setValue($apiActionTest, $restApiClient);

        $getRestApiClientReflection = new \ReflectionMethod(ApiActionTest::class, 'getRestApiClient');
        $getRestApiClientReflection->setAccessible(true);

        $this->assertEquals($restApiClient, $getRestApiClientReflection->invoke($apiActionTest));
    }

    /**
     * Test ApiActionTrait::getRestApiClient() method throw an exception if rest api client is not configured.
     */
    public function testRestApiClientGetterException()
    {
        $this->expectException(\BadMethodCallException::class);

        $getRestApiClientReflection = new \ReflectionMethod(ApiActionTest::class, 'getRestApiClient');
        $getRestApiClientReflection->setAccessible(true);
        $getRestApiClientReflection->invoke(new ApiActionTest());
    }

    /**
     * Test ApiActionTrait::setSerializer() method.
     */
    public function testSerializerSetter()
    {
        $serializer = $this->createMock(SerializerInterface::class);

        $apiActionTest = new ApiActionTest();
        $apiActionTest->setSerializer($serializer);

        $serializerReflection = new \ReflectionProperty(ApiActionTest::class, 'serializer');
        $serializerReflection->setAccessible(true);

        $this->assertEquals($serializer, $serializerReflection->getValue($apiActionTest));
    }

    /**
     * Test ApiActionTrait::getSerializer() method.
     */
    public function testSerializerGetter()
    {
        $serializer = $this->createMock(SerializerInterface::class);

        $apiActionTest = new ApiActionTest();

        $serializerReflection = new \ReflectionProperty(ApiActionTest::class, 'serializer');
        $serializerReflection->setAccessible(true);
        $serializerReflection->setValue($apiActionTest, $serializer);

        $getSerializerReflection = new \ReflectionMethod(ApiActionTest::class, 'getSerializer');
        $getSerializerReflection->setAccessible(true);

        $this->assertEquals($serializer, $getSerializerReflection->invoke($apiActionTest));
    }

    /**
     * Test ApiActionTrait::getSerializer() method throw an exception if serializer is not configured.
     */
    public function testSerializerGetterException()
    {
        $this->expectException(\BadMethodCallException::class);

        $getSerializerReflection = new \ReflectionMethod(ApiActionTest::class, 'getSerializer');
        $getSerializerReflection->setAccessible(true);
        $getSerializerReflection->invoke(new ApiActionTest());
    }
}

/**
 * Class ApiActionTest.
 * Used only to test the trait.
 */
class ApiActionTest
{
    use ApiActionTrait;
}
