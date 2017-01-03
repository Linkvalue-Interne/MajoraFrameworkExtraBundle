<?php

namespace Majora\Framework\Tests\Serializer\Handler\Collection;

use Majora\Framework\Serializer\Handler\Collection\CollectionHandler;
use Majora\Framework\Serializer\Model\SerializableInterface;
use Majora\Framework\Tests\Serializer\Model\SerializableMock1;
use PHPUnit_Framework_TestCase;

/**
 * Unit test class for CollectionHandler.php.
 *
 * @see   \Majora\Framework\Serializer\Handler\Collection\CollectionHandler
 *
 * @group legacy
 */
class CollectionHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * tests serialize() method.
     *
     * @dataProvider testSerializeProvider
     */
    public function testSerialize($object, $scope, $output)
    {
        $collectionHandler = new CollectionHandler();

        $this->assertEquals(
            $output,
            $collectionHandler->serialize($object, $scope)
        );
    }

    public function testSerializeProvider()
    {
        $cases = [
            'string_as_array' => ['string', 'test', ['string']],
            'int_as_array' => [42, 'test', [42]],
            'array_as_array' => [
                ['hello', 'foo' => 'bar', 42],
                'test',
                ['hello', 'foo' => 'bar', 42],
            ],
        ];

        $scope = 'test';
        $object = $this->prophesize('StdClass');
        $object->willImplement(SerializableInterface::class);
        $object->serialize($scope)->willReturnArgument(0);

        $cases['array_as_serialized'] = [$object->reveal(), $scope, $scope];

        return $cases;
    }

    /**
     * tests deserialize() method.
     *
     * @dataProvider testDeserializeProvider
     */
    public function testDeserialize($data, $type, $output)
    {
        $collectionHandler = new CollectionHandler();

        $this->assertEquals(
            $output,
            $collectionHandler->deserialize($data, $type)
        );
    }

    public function testDeserializeProvider()
    {
        return [
            'not_an_object' => [123, 'integer', 123],
            'inexistant_object' => ['biggoron', 'F*ckingBigSword', 'biggoron'],
            'not_serializable' => [['ganon' => 'dorf'], 'StdClass', new \StdClass()],
            'serializable' => [
                ['id' => 42],
                SerializableMock1::class,
                (new SerializableMock1())->setId(42),
            ],
        ];
    }
}
