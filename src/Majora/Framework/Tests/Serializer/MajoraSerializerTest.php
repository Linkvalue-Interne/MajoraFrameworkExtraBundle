<?php

namespace Majora\Framework\Tests\Serializer;

use Majora\Framework\Serializer\MajoraSerializer;
use PHPUnit_Framework_TestCase;
use Prophecy\Argument;

/**
 * Unit test class for MajoraSerializer.php.
 *
 * @see   \Majora\Framework\Serializer\MajoraSerializer
 *
 * @group legacy
 */
class MajoraSerializerTest extends PHPUnit_Framework_TestCase
{
    /**
     * tests serialize methods without any handler defined.
     *
     * @expectedException              \BadMethodCallException
     * @expectedExceptionMessageRegExp #Unsupported format "test"*#
     */
    public function testSerializeWithoutHandlers()
    {
        $serializer = new MajoraSerializer([]);
        $serializer->serialize(new \StdClass(), 'test');
    }

    /**
     * tests serialize cases.
     *
     * @dataProvider serializeProvider
     */
    public function testSerialize(
        array $formatHandlers,
        $data,
        $format,
        array $context,
        $expectedReturn
    ) {
        $serializer = new MajoraSerializer($formatHandlers);
        $this->assertEquals(
            $expectedReturn,
            $serializer->serialize($data, $format, $context)
        );
    }

    public function serializeProvider()
    {
        $cases = [];

        // Full case : 2 handlers and a scope
        $handler1 = $this->prophesize('Majora\Framework\Serializer\Handler\FormatHandlerInterface');
        $handler1->serialize('123', 'scope')
            ->willReturnArgument(0)
            ->shouldBeCalled();

        $handler2 = $this->prophesize('Majora\Framework\Serializer\Handler\FormatHandlerInterface');
        $handler2->serialize(Argument::any(), Argument::any())
            ->shouldNotBeCalled();

        $cases[] = [
            ['json' => $handler1->reveal(), 'yaml' => $handler2->reveal()],
            '123',
            'json',
            ['scope' => 'scope'],
            '123',
        ];

        // Default case, 2 handlers and no scope
        $handler1 = $this->prophesize('Majora\Framework\Serializer\Handler\FormatHandlerInterface');
        $handler1->serialize(Argument::any(), Argument::any())
            ->shouldNotBeCalled();

        $handler2 = $this->prophesize('Majora\Framework\Serializer\Handler\FormatHandlerInterface');
        $handler2->serialize('123', 'default')
            ->willReturnArgument(0)
            ->shouldBeCalled();

        $cases[] = [
            ['json' => $handler1->reveal(), 'yaml' => $handler2->reveal()],
            '123',
            'yaml',
            [],
            '123',
        ];

        return $cases;
    }

    /**
     * tests deserialize methods without any handler defined.
     *
     * @expectedException              \BadMethodCallException
     * @expectedExceptionMessageRegExp #Unsupported format "test"*#
     */
    public function testDeserializeWithoutHandlers()
    {
        $serializer = new MajoraSerializer([]);
        $serializer->deserialize('hello', 'StdClass', 'test', []);
    }

    /**
     * tests deserialize method.
     */
    public function testDeserialize()
    {
        $handler1 = $this->prophesize('Majora\Framework\Serializer\Handler\FormatHandlerInterface');
        $handler1->deserialize('123', 'StdClass')
            ->willReturnArgument(0)
            ->shouldBeCalled();

        $handler2 = $this->prophesize('Majora\Framework\Serializer\Handler\FormatHandlerInterface');
        $handler2->deserialize(Argument::any(), Argument::any())
            ->shouldNotBeCalled();

        $serializer = new MajoraSerializer(
            [
                'json' => $handler1->reveal(),
                'yaml' => $handler2->reveal(),
            ]
        );

        $this->assertEquals(
            '123',
            $serializer->deserialize('123', 'StdClass', 'json')
        );
    }
}
