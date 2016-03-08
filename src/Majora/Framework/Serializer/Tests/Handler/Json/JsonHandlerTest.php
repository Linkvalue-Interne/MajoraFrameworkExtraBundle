<?php

namespace Majora\Framework\Serializer\Tests\Handler\Collection;

use Majora\Framework\Normalizer\MajoraNormalizer;
use Majora\Framework\Normalizer\Model\NormalizableInterface;
use Majora\Framework\Serializer\Handler\Json\JsonHandler;
use Majora\Framework\Serializer\Tests\Model\SerializableMock1;
use PHPUnit_Framework_TestCase;

/**
 * Unit test class for JsonHandler.php.
 *
 * @see Majora\Framework\Serializer\Handler\Json\JsonHandler
 */
class JsonHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * tests serialize() method.
     *
     * @dataProvider serializationSuccessCaseProvider
     */
    public function testSerialize($normalizedData, $json)
    {
        $object = $this->prophesize('StdClass');
        $object->willImplement(NormalizableInterface::class);
        $object = $object->reveal();

        $scope = 'test';

        $normalizer = $this->prophesize(MajoraNormalizer::class);
        $normalizer->normalize($object, $scope)
            ->willReturn($normalizedData)
            ->shouldBeCalled()
        ;

        $jsonHandler = new JsonHandler($normalizer->reveal());

        $this->assertEquals(
            $json,
            $jsonHandler->serialize($object, $scope)
        );
    }

    public function serializationSuccessCaseProvider()
    {
        return array(
            'string_as_array' => array(array('string'), '["string"]'),
            'int_as_array' => array(array(42), '[42]'),
            'array_as_array' => array(
                $raw = array('hello', 'foo' => 'bar', 42, 'nested' => array('child' => 'value')),
                json_encode($raw)
            )
        );
    }

    /**
     * tests deserialize() json decoding exception.
     *
     * @expectedException              Majora\Framework\Serializer\Handler\Json\Exception\JsonDeserializationException
     * @expectedExceptionMessageRegExp #Invalid json data, error*#
     */
    public function testDecodeException()
    {
        $collectionHandler = new JsonHandler(
            $this->prophesize(MajoraNormalizer::class)->reveal()
        );
        $collectionHandler->deserialize('THIS IS NOT JSOOOOOOOOON !', 'StdClass');
    }

    /**
     * tests deserialize() method.
     *
     * @dataProvider deserializationSuccessCaseProvider
     */
    public function testDeserialize($json, $normalizedData)
    {
        $normalizer = $this->prophesize(MajoraNormalizer::class);
        $normalizer->denormalize($normalizedData, \StdClass::class)
            ->shouldBeCalled()
        ;

        $collectionHandler = new JsonHandler($normalizer->reveal());
        $collectionHandler->deserialize($json, \StdClass::class);
    }

    public function deserializationSuccessCaseProvider()
    {
        return array(
            'string_as_array' => array('["string"]', array('string')),
            'int_as_array' => array('[42]', array(42)),
            'array_as_array' => array(
                json_encode($raw = array('hello', 'foo' => 'bar', 42, 'nested' => array('child' => 'value'))),
                $raw
            )
        );
    }
}
