<?php

namespace Majora\Framework\Serializer\Tests\Handler\Collection;

use Majora\Framework\Serializer\Handler\Json\JsonHandler;
use Majora\Framework\Serializer\Tests\Model\SerializableMock1;
use PHPUnit_Framework_TestCase;

/**
 * Unit test class for JsonHandler.php.
 *
 * @see Majora\Framework\Serializer\Handler\Json\JsonHandler
 */
class JsonHandlerTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * tests serialize() method.
     *
     * @dataProvider testSerializeProvider
     */
    public function testSerialize($object, $scope, $output)
    {
        $collectionHandler = new JsonHandler();

        $this->assertEquals(
            $output,
            $collectionHandler->serialize($object, $scope)
        );
    }

    public function testSerializeProvider()
    {
        $cases = array(
            'string_as_array' => array('string', 'test', json_encode(array('string'))),
            'int_as_array'    => array(42, 'test', json_encode(array(42))),
            'array_as_array'  => array(
                array('hello', 'foo' => 'bar', 42),
                'test',
                json_encode(array('hello', 'foo' => 'bar', 42)),
            ),
        );

        $scope = 'test';
        $object = $this->prophesize('StdClass');
        $object->willImplement('Majora\Framework\Serializer\Model\SerializableInterface');
        $object->serialize($scope)->willReturnArgument(0);

        $cases['array_as_serialized'] = array($object->reveal(), $scope, json_encode($scope));

        return $cases;
    }

    /**
     * tests deserialize() json decoding exception.
     *
     * @expectedException              Majora\Framework\Serializer\Handler\Json\Exception\JsonDeserializationException
     * @expectedExceptionMessageRegExp #Invalid json data, error*#
     */
    public function testDecodeException()
    {
        $collectionHandler = new JsonHandler();
        $collectionHandler->deserialize('THIS IS NOT JSOOOOOOOOON !', 'StdClass');
    }

    /**
     * tests deserialize() method.
     *
     * @dataProvider testDeserializeProvider
     */
    public function testDeserialize($data, $type, $output)
    {
        $collectionHandler = new JsonHandler();

        $this->assertEquals(
            $output,
            $collectionHandler->deserialize($data, $type)
        );
    }

    public function testDeserializeProvider()
    {
        return array(
            'not_an_object'     => array(json_encode(123), 'integer', 123),
            'inexistant_object' => array(json_encode('biggoron'), 'F*ckingBigSword', 'biggoron'),
            'not_serializable'  => array(json_encode(array('ganon' => 'dorf')), 'StdClass', new \StdClass()),
            'serializable'      => array(
                json_encode(array('id' => 42)),
                'Majora\Framework\Serializer\Tests\Model\SerializableMock1',
                (new SerializableMock1())->setId(42),
            ),
        );
    }
}
