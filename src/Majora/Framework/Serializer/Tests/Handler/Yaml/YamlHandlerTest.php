<?php

namespace Majora\Framework\Serializer\Tests\Handler\Yaml;

use Majora\Framework\Serializer\Handler\Yaml\YamlHandler;
use Majora\Framework\Serializer\Tests\Model\SerializableMock1;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Yaml\Yaml;

/**
 * Unit test class for YamlHandler.php.
 *
 * @see Majora\Framework\Serializer\Handler\Collection\YamlHandler
 */
class YamlHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * tests serialize() method.
     *
     * @dataProvider testSerializeProvider
     */
    public function testSerialize($object, $scope, $output)
    {
        $collectionHandler = new YamlHandler(new Yaml());

        $this->assertEquals(
            $output,
            $collectionHandler->serialize($object, $scope)
        );
    }

    public function testSerializeProvider()
    {
        $cases = array(
            'string_as_array' => array('string', 'test', "- string\n"),
            'int_as_array'    => array(42, 'test', "- 42\n"),
            'array_as_array'  => array(
                array('hello', 'foo' => 'bar', 42),
                'test',
                "0: hello\nfoo: bar\n1: 42\n",
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
        $collectionHandler = new YamlHandler(new Yaml());

        $this->assertEquals(
            $output,
            $collectionHandler->deserialize($data, $type)
        );
    }

    public function testDeserializeProvider()
    {
        return array(
            'not_an_object'     => array('123', 'integer', 123),
            'inexistant_object' => array('biggoron', 'F*ckingBigSword', 'biggoron'),
            'not_serializable'  => array('ganon: dorf', 'StdClass', new \StdClass()),
            'serializable'      => array(
                'id: 42',
                'Majora\Framework\Serializer\Tests\Model\SerializableMock1',
                (new SerializableMock1())->setId(42),
            ),
        );
    }
}
