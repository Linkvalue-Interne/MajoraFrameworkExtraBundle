<?php

namespace Majora\Framework\Tests\Inflector;

use Majora\Framework\Inflector\Inflector;

/**
 * Class InflectorTest.
 *
 * @see \Majora\Framework\Inflector\Inflector
 */
class InflectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Inflector
     */
    private $inflector;

    /**
     * @var array
     */
    private $replacements;

    /**
     * Sets up.
     */
    protected function setUp()
    {
        $this->replacements = [
            'myLast_name' => 'myFirst_name',
        ];

        $this->inflector = new Inflector($this->replacements);
    }

    /**
     * Tears down.
     */
    protected function tearDown()
    {
        unset(
            $this->inflector,
            $this->replacements
        );
    }

    /**
     * Test Inflector::constructor() method.
     */
    public function testConstructor()
    {
        $replacementsReflection = new \ReflectionProperty(Inflector::class, 'replacements');
        $replacementsReflection->setAccessible(true);
        $replacements = $replacementsReflection->getValue($this->inflector);

        $this->assertEquals(
            [
                'myLastName' => 'myFirstName',
                'MyLastName' => 'MyFirstName',
                'my_last_name' => 'my_first_name',
                'my-last-name' => 'my-first-name',
                'MY_LAST_NAME' => 'MY_FIRST_NAME',
            ],
            $replacements
        );
    }

    /**
     * Test Inflector::all() method.
     */
    public function testAll()
    {
        $replacements = ['test'];

        $replacementsReflection = new \ReflectionProperty(Inflector::class, 'replacements');
        $replacementsReflection->setAccessible(true);
        $replacementsReflection->setValue($this->inflector, $replacements);

        $this->assertEquals($replacements, $this->inflector->all());
    }

    /**
     * Test Inflector::translate() method.
     */
    public function testTranslate()
    {
        $this->assertEquals('my_first_name', $this->inflector->translate('my_last_name'));
    }

    /**
     * Test Inflector::camelize() method.
     */
    public function testCamelize()
    {
        $this->assertEquals('thisString', $this->inflector->camelize('this_string'));
    }

    /**
     * Test Inflector::pascalize() method.
     */
    public function testPascalize()
    {
        $this->assertEquals('ThisString', $this->inflector->pascalize('this_string'));
    }

    /**
     * Test Inflector::snakelize() method.
     */
    public function testSnakelize()
    {
        $this->assertEquals('this_string', $this->inflector->snakelize('thisString'));
    }

    /**
     * Test Inflector::spinalize() method.
     */
    public function testSpinalize()
    {
        $this->assertEquals('this-string', $this->inflector->spinalize('thisString'));
    }

    /**
     * Test Inflector::uppercase() method.
     */
    public function testUppercase()
    {
        $this->assertEquals('THIS_STRING', $this->inflector->uppercase('this_string'));
    }

    /**
     * Test Inflector::slugify() method.
     */
    public function testSlugify()
    {
        $this->assertEquals(
            'baguette-croissants-pain-d-epice',
            $this->inflector->slugify('baguette Croissants pain d\'épice')
        );
    }

    /**
     * Test Inflector::normalize() method.
     */
    public function testNormalize()
    {
        $data1 = 'my_last_name';
        $data2 = [
            'my_last_name' => 'My last name',
            'my_first_name' => 'My first name',
            'childrens' => [
                'child_name_1' => 'Child name 1',
                'child_name_2' => 'Child name 2',
            ],
        ];

        $this->assertEquals('myLastName', $this->inflector->normalize($data1, 'camelize'));

        $this->assertEquals(
            [
                'myLastName' => 'My last name',
                'myFirstName' => 'My first name',
                'childrens' => [
                    'childName1' => 'Child name 1',
                    'childName2' => 'Child name 2',
                ],
            ],
            $this->inflector->normalize($data2, 'camelize')
        );
    }

    /**
     * Test Inflector::normalize() method exception.
     */
    public function testNormalizeException()
    {
        $data = [
            'my_last_name' => 'My last name',
            'myLastName' => 'My last name',
            'my_first_name' => 'My first name',
        ];

        $this->expectException(\InvalidArgumentException::class);

        $this->inflector->normalize($data, 'camelize');
    }

    /**
     * Test Inflector::directorize() method.
     */
    public function testDirectorize()
    {
        $this->assertEquals(
            'path' . DIRECTORY_SEPARATOR . 'to' . DIRECTORY_SEPARATOR . 'the' . DIRECTORY_SEPARATOR . 'file',
            $this->inflector->directorize('path\to\the\file')
        );
    }

    /**
     * Test Inflector::unixizePath() method.
     */
    public function testUnixizePath()
    {
        $this->assertEquals('/home', $this->inflector->unixizePath('C:\home'));
    }
}
