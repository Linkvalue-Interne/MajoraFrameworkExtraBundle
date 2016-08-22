<?php

namespace Majora\Framework\Serializer\Tests\Handler\Yaml;

use Majora\Framework\Normalizer\MajoraNormalizer;
use Majora\Framework\Normalizer\Model\NormalizableInterface;
use Majora\Framework\Serializer\Handler\Yaml\YamlHandler;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Yaml\Yaml;

/**
 * Unit test class for YamlHandler.php.
 *
 * @see Majora\Framework\Serializer\Handler\Collection\YamlHandler
 *
 * @group legacy
 */
class YamlHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * tests serialize() method.
     *
     * @dataProvider serializationSuccessCaseProvider
     */
    public function testSerialize($normalizedData, $output)
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

        $collectionHandler = new YamlHandler(
            new Yaml(),
            $normalizer->reveal()
        );

        $this->assertEquals(
            $output,
            $collectionHandler->serialize($object, $scope)
        );
    }

    public function serializationSuccessCaseProvider()
    {
        return array(
            'string_as_array' => array(array('string'), "- string\n"),
            'int_as_array' => array(array(42), "- 42\n"),
            'array_as_array' => array(
                array('hello', 'foo' => 'bar', 42),
                "0: hello\nfoo: bar\n1: 42\n",
            ),
        );
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

        $collectionHandler = new YamlHandler(
            new Yaml(),
            $normalizer->reveal()
        );
        $collectionHandler->deserialize($json, \StdClass::class);
    }

    public function deserializationSuccessCaseProvider()
    {
        return array(
            'string_as_array' => array("- string\n", array('string')),
            'int_as_array' => array("- 42\n", array(42)),
            'array_as_array' => array(
                "0: hello\nfoo: bar\n1: 42\n",
                array('hello', 'foo' => 'bar', 42),
            ),
        );
    }
}
