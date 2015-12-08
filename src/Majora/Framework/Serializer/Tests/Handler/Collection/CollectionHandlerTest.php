<?php

namespace Majora\Framework\Serializer\Tests\Handler\Collection;

use Majora\Framework\Serializer\Handler\Collection\CollectionHandler;
use Majora\Framework\Serializer\Tests\Model\SerializableMock1;
use PHPUnit_Framework_TestCase;

/**
 * Unit test class for CollectionHandler.php.
 *
 * @see Majora\Framework\Serializer\Handler\Collection\CollectionHandler
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
        $cases = array(
            'string_as_array' => array('string', 'test', array('string')),
            'int_as_array'    => array(42, 'test', array(42)),
            'array_as_array'  => array(
                array('hello', 'foo' => 'bar', 42),
                'test',
                array('hello', 'foo' => 'bar', 42),
            ),
        );

        $scope = 'test';
        $object = $this->prophesize('StdClass');
        $object->willImplement('Majora\Framework\Serializer\Model\SerializableInterface');
        $object->serialize($scope)->willReturnArgument(0);

        $cases['array_as_serialized'] = array($object->reveal(), $scope, $scope);

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
        return array(
            'not_an_object'     => array(123, 'integer', 123),
            'inexistant_object' => array('biggoron', 'F*ckingBigSword', 'biggoron'),
            'not_serializable'  => array(array('ganon' => 'dorf'), 'StdClass', new \StdClass()),
            'serializable'      => array(
                array('id' => 42),
                'Majora\Framework\Serializer\Tests\Model\SerializableMock1',
                (new SerializableMock1())->setId(42),
            ),
        );
    }
}
