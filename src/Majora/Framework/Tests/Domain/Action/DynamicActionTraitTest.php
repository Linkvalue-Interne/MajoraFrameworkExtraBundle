<?php

namespace Majora\Framework\Tests\Domain\Action;

use Doctrine\Common\Collections\ArrayCollection;
use Majora\Framework\Domain\Action\DynamicActionTrait;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Class DynamicActionTraitTest.
 *
 * @see \Majora\Framework\Domain\Action\DynamicActionTrait
 */
class DynamicActionTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test method DynamicActionTrait::__call() method throw an exception if it is not a set or a get method called.
     */
    public function testMagicCallException()
    {
        $this->expectException(\BadMethodCallException::class);

        $dynamicAction = new DynamicActionTest();
        $dynamicAction->hasTest();
    }

    /**
     * Test method DynamicActionTrait::getAttributes() method.
     */
    public function testAttributesGetter()
    {
        $attributes = new ArrayCollection();
        $attributes->set('test_key', 'test_value');

        $dynamicAction = new DynamicActionTest();

        $attributesReflection = new \ReflectionProperty(DynamicActionTest::class, 'attributes');
        $attributesReflection->setAccessible(true);
        $attributesReflection->setValue($dynamicAction, $attributes);

        $getAttributesReflection = new \ReflectionMethod(DynamicActionTest::class, 'getAttributes');
        $getAttributesReflection->setAccessible(true);

        $this->assertEquals($attributes, $getAttributesReflection->invoke($dynamicAction));
    }

    /**
     * Test method DynamicActionTrait::getAttributes() method with empty attributes.
     */
    public function testAttributesOnEmptyAttributesGetter()
    {
        $getAttributesReflection = new \ReflectionMethod(DynamicActionTest::class, 'getAttributes');
        $getAttributesReflection->setAccessible(true);

        $attributes = $getAttributesReflection->invoke(new DynamicActionTest());

        $this->assertInstanceOf(ArrayCollection::class, $attributes);
        $this->assertTrue($attributes->isEmpty());
    }

    /**
     * Test method DynamicActionTrait::getPropertyAccessor() method.
     */
    public function testPropertyAccessorGetter()
    {
        $getPropertyAccessorReflection = new \ReflectionMethod(DynamicActionTest::class, 'getPropertyAccessor');
        $getPropertyAccessorReflection->setAccessible(true);

        $this->assertInstanceOf(
            PropertyAccessor::class,
            $getPropertyAccessorReflection->invoke(new DynamicActionTest())
        );
    }

    /**
     * Test DynamicActionTrait::_get() method.
     */
    public function testGet()
    {
        $attributes = $this->createMock(ArrayCollection::class);
        $dynamicAction = new DynamicActionTest();

        $attributesReflection = new \ReflectionProperty(DynamicActionTest::class, 'attributes');
        $attributesReflection->setAccessible(true);
        $attributesReflection->setValue($dynamicAction, $attributes);

        $attributes
            ->expects($this->once())
            ->method('get')
            ->with('test');

        $dynamicAction->getTest();
    }

    /**
     * Test DynamicActionTrait::_has() method.
     */
    public function testHas()
    {
        $attributes = $this->createMock(ArrayCollection::class);
        $dynamicAction = new DynamicActionTest();

        $attributesReflection = new \ReflectionProperty(DynamicActionTest::class, 'attributes');
        $attributesReflection->setAccessible(true);
        $attributesReflection->setValue($dynamicAction, $attributes);

        $attributes
            ->expects($this->once())
            ->method('containsKey')
            ->with('test');

        $hasReflection = new \ReflectionMethod(DynamicActionTest::class, '_has');
        $hasReflection->setAccessible(true);
        $hasReflection->invokeArgs($dynamicAction, ['test']);
    }

    /**
     * Test DynamicActionTrait::_set() method.
     */
    public function testSet()
    {
        $attributes = $this->createMock(ArrayCollection::class);
        $dynamicAction = new DynamicActionTest();

        $attributesReflection = new \ReflectionProperty(DynamicActionTest::class, 'attributes');
        $attributesReflection->setAccessible(true);
        $attributesReflection->setValue($dynamicAction, $attributes);

        $attributes
            ->expects($this->once())
            ->method('set')
            ->with('test', 'my_value');

        $dynamicAction->setTest('my_value');
    }

    /**
     * Test DynamicActionTrait::setIfDefined() method.
     */
    public function testSetIfDefined()
    {
        $propertyAccessor = $this->createMock(PropertyAccessorInterface::class);
        $dynamicAction = new DynamicActionTest();

        $propertyAccessorReflection = new \ReflectionProperty(DynamicActionTest::class, 'propertyAccessor');
        $propertyAccessorReflection->setAccessible(true);
        $propertyAccessorReflection->setValue($dynamicAction, $propertyAccessor);

        $setIfDefinedReflection = new \ReflectionMethod(DynamicActionTest::class, 'setIfDefined');
        $setIfDefinedReflection->setAccessible(true);

        $dynamicAction->setName('test');
        $obj = new ObjectTest();

        $propertyAccessor
            ->expects($this->once())
            ->method('setValue')
            ->with($obj, 'name', 'test');

        $this->assertEquals('test', $setIfDefinedReflection->invokeArgs($dynamicAction, [$obj, 'name']));
    }

    /**
     * Test DynamicActionTrait::setIfDefined() method if the value in attributes is missing.
     */
    public function testSetIfDefinedWithMissingValue()
    {
        $propertyAccessor = $this->createMock(PropertyAccessorInterface::class);
        $dynamicAction = new DynamicActionTest();

        $propertyAccessorReflection = new \ReflectionProperty(DynamicActionTest::class, 'propertyAccessor');
        $propertyAccessorReflection->setAccessible(true);
        $propertyAccessorReflection->setValue($dynamicAction, $propertyAccessor);

        $setIfDefinedReflection = new \ReflectionMethod(DynamicActionTest::class, 'setIfDefined');
        $setIfDefinedReflection->setAccessible(true);

        $obj = new ObjectTest();

        $this->assertNull($setIfDefinedReflection->invokeArgs($dynamicAction, [$obj, 'name']));
    }

    /**
     * Test DynamicActionTrait::getScopes() method.
     */
    public function testGetScopes()
    {
        $this->assertTrue(is_array(DynamicActionTrait::getScopes()));
    }

    /**
     * Test DynamicActionTrait::normalize() method.
     */
    public function testNormalize()
    {
        $dynamicAction = new DynamicActionTest();

        $dynamicAction
            ->setLastName('my_last_name')
            ->setFirstName('my_first_name');

        $this->assertEquals(
            [
                'lastName' => 'my_last_name',
                'firstName' => 'my_first_name',
            ],
            $dynamicAction->normalize('test')
        );

        $this->assertEquals(
            [
                'lastName' => 'my_last_name',
            ],
            $dynamicAction->normalize()
        );

        $this->assertEquals(
            [
                'lastName' => 'my_last_name',
                'phone' => null,
            ],
            $dynamicAction->normalize('phone')
        );
    }

    /**
     * Test DynamicActionTrait::denormalize() method.
     */
    public function testDenormalize()
    {
        $data = [
            'test1' => 'value1',
            'test2' => 'value2',
            'test3' => 'value3',
        ];

        $dynamicAction = new DynamicActionTest();
        $dynamicAction->denormalize($data);

        $attributesReflection = new \ReflectionProperty(DynamicActionTest::class, 'attributes');
        $attributesReflection->setAccessible(true);

        $this->assertEquals(new ArrayCollection($data), $attributesReflection->getValue($dynamicAction));
    }

    /**
     * Test DynamicActionTrait::serialize() method.
     */
    public function testLegacySerialize()
    {
        $dynamicAction = $this
            ->getMockBuilder(DynamicActionTrait::class)
            ->setMethodsExcept(['serialize'])
            ->getMockForTrait();

        $scope = 'my_scope';

        $dynamicAction
            ->expects($this->once())
            ->method('normalize')
            ->with($scope);

        $dynamicAction->serialize($scope);
    }

    /**
     * Test DynamicActionTrait::deserialize() method.
     */
    public function testLegacyDeserialize()
    {
        $dynamicAction = $this
            ->getMockBuilder(DynamicActionTrait::class)
            ->setMethodsExcept(['deserialize'])
            ->getMockForTrait();

        $data = [
            'test1' => 'value1',
            'test2' => 'value2',
            'test3' => 'value3',
        ];

        $dynamicAction
            ->expects($this->once())
            ->method('denormalize')
            ->with($data);

        $dynamicAction->deserialize($data);
    }
}

/**
 * Class DynamicActionTest.
 * Used only to test the trait.
 */
class DynamicActionTest
{
    use DynamicActionTrait;

    public static function getScopes()
    {
        return [
            'default' => ['lastName'],
            'phone' => ['lastName', 'phone'],
        ];
    }
}

/**
 * Class ObjectTest.
 * Used only to test the trait.
 */
class ObjectTest
{
    public $name;
}
