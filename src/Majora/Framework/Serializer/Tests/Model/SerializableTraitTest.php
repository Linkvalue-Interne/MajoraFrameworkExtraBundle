<?php

namespace Majora\Framework\Serializer\Tests\Model;

use PHPUnit_Framework_TestCase;

/**
 * Test class for serializable trait.
 */
class SerializableTraitTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * tests serialize methods with an unsupported scope.
     *
     * @expectedException              InvalidArgumentException
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
        return array(
            array('default', array('id' => 1, 'label' => 'mock_1_label')),
            array('id', 1),
            array('full', array(
                'id' => 1,
                'label' => 'mock_1_label',
                'table' => array('mock_1_1', 'mock_1_2'),
                'mock2' => 2,
                'date'  => (new \DateTime('2015-01-01'))->format(\DateTime::ISO8601),
            )),
            array('extra', array(
                'id' => 1,
                'label' => 'mock_1_label',
                'table' => array('mock_1_1', 'mock_1_2'),
                'mock2' => array(
                    'id' => 2,
                    'label' => 'mock_2_label',
                    'table' => array('mock_2_1', 'mock_2_1'),
                ),
                'date'  => (new \DateTime('2015-01-01'))->format(\DateTime::ISO8601),
            )),
        );
    }

    /**
     * tests unserialize methods with an unsupported scope.
     *
     * @expectedException              InvalidArgumentException
     * @expectedExceptionMessageRegExp #Unable to set "fake_member" property into a ".+" object, any existing property path to define it in.#
     */
    public function testUnsupportedDeserializeData()
    {
        $entityMock = new SerializableMock1();
        $entityMock->deserialize(array(
            'fake_member' => 'fake_data',
        ));
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
        return array(

            // simple case : simple types
            array(
                array('id' => 50, 'label' => 'zelda', 'table' => array('ocarina' => 'of-time')),
                (new SerializableMock1())
                    ->setId(50)
                    ->setLabel('zelda')
                    ->setTable(array('ocarina' => 'of-time')),
            ),

            // test direct property setting method
            array(
                array('id' => 60, 'protect' => 'link'),
                (new SerializableMock1())
                    ->setId(60)
                    ->setProtectedProtect('link'),
            ),

            // test with a non serializable class
            array(
                array('id' => 70, 'mock3' => array('ganon' => 'dorf')),
                (new SerializableMock1())
                    ->setId(70)
                    ->setMock3(new \StdClass()),
            ),

            // test with a datetime
            array(
                array('id' => 75, 'date' => (new \DateTime('2015-07-01'))->format(\DateTime::ISO8601)),
                (new SerializableMock1())
                    ->setId(75)
                    ->setDate(new \DateTime('2015-07-01')),
            ),

            // tests with unsupported hinting
            array(
                array('id' => 80, 'callback' => array($this, 'serializedDataProvider')),
                (new SerializableMock1())
                    ->setId(80)
                    ->setCallback(array($this, 'serializedDataProvider')),
            ),

            // serializable child class
            array(
                array('id' => 90, 'mock2' => array('id' => 100)),
                (new SerializableMock1())
                    ->setId(90)
                    ->setMock2((new SerializableMock2())->setId(100)),
            ),
        );
    }
}
