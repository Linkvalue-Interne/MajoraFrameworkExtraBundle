<?php

namespace Majora\Framework\Tests\Serializer\Model;

use PHPUnit_Framework_TestCase;

/**
 * Test class for serializable trait.
 *
 * @group legacy
 */
class SerializableTraitTest extends PHPUnit_Framework_TestCase
{
    /**
     * tests serialize methods with an unsupported scope.
     *
     * @expectedException             \InvalidArgumentException
     * @expectedExceptionMessageRegExp #Invalid scope for .+ "fake_scope" given.#
     */
    public function testUnsupportedSerializeScope()
    {
        $entityMock = new SerializableMock1();
        $entityMock->serialize('fake_scope');
    }

    /**
     * tests serialize trait method, on a mocked class using it.
     *
     * @dataProvider scopeProvider
     */
    public function testSerialize($scope, $expectedData)
    {
        $entityMock = new SerializableMock1();
        $this->assertEquals(
            $expectedData,
            $entityMock->serialize($scope)
        );
    }

    public function scopeProvider()
    {
        return [
            ['default', ['id' => 1, 'label' => 'mock_1_label']],
            ['id', 1],
            [
                'full',
                [
                    'id' => 1,
                    'label' => 'mock_1_label',
                    'table' => ['mock_1_1', 'mock_1_2'],
                    'mock2' => 2,
                    'optionnal_defined' => 'optionnal_mocked',
                    'date' => (new \DateTime('2015-01-01'))->format(\DateTime::ISO8601),
                ],
            ],
            [
                'extra',
                [
                    'id' => 1,
                    'label' => 'mock_1_label',
                    'table' => ['mock_1_1', 'mock_1_2'],
                    'mock2' => [
                        'id' => 2,
                        'label' => 'mock_2_label',
                        'table' => ['mock_2_1', 'mock_2_1'],
                    ],
                    'optionnal_defined' => 'optionnal_mocked',
                    'date' => (new \DateTime('2015-01-01'))->format(\DateTime::ISO8601),
                ],
            ],
        ];
    }

    /**
     * tests unserialize methods with an unsupported scope.
     *
     * @expectedException              \InvalidArgumentException
     * @expectedExceptionMessageRegExp #Unable to set "fake_member" property into a ".+" object, any existing property path to write it in.#
     */
    public function testUnsupportedDeserializeData()
    {
        $entityMock = new SerializableMock1();
        $entityMock->deserialize(
            [
                'fake_member' => 'fake_data',
            ]
        );
    }

    /**
     * tests deserialize trait method, with a mocked class using it.
     *
     * @dataProvider serializedDataProvider
     */
    public function testDeserialize($data, $expectedObject)
    {
        $entityMock = new SerializableMock1();
        $this->assertEquals(
            $expectedObject,
            $entityMock->deserialize($data)
        );
    }

    public function serializedDataProvider()
    {
        $ganonDorf = new SimpleClassMock();
        $ganonDorf->ganon = 'dorf';

        return [

            // simple case : simple types
            [
                ['id' => 50, 'label' => 'zelda', 'table' => ['ocarina' => 'of-time']],
                (new SerializableMock1())
                    ->setId(50)
                    ->setLabel('zelda')
                    ->setTable(['ocarina' => 'of-time']),
            ],

            // test direct property setting method
            [
                ['id' => 60, 'protect' => 'link'],
                (new SerializableMock1())
                    ->setId(60)
                    ->setProtectedProtect('link'),
            ],

            // test with a non serializable class
            [
                ['id' => 70, 'mock3' => ['ganon' => 'dorf']],
                (new SerializableMock1())
                    ->setId(70)
                    ->setMock3($ganonDorf),
            ],

            // test with a datetime
            [
                ['id' => 75, 'date' => (new \DateTime('2015-07-01'))->format(\DateTime::ISO8601)],
                (new SerializableMock1())
                    ->setId(75)
                    ->setDate(new \DateTime('2015-07-01')),
            ],

            // tests with unsupported hinting
            [
                ['id' => 80, 'callback' => [$this, 'serializedDataProvider']],
                (new SerializableMock1())
                    ->setId(80)
                    ->setCallback([$this, 'serializedDataProvider']),
            ],

            // serializable child class
            [
                ['id' => 90, 'mock2' => ['id' => 100]],
                (new SerializableMock1())
                    ->setId(90)
                    ->setMock2((new SerializableMock2())->setId(100)),
            ],
        ];
    }
}
