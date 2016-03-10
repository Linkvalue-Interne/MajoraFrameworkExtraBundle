<?php

namespace Majora\Framework\Normalizer\Tests;

use Majora\Framework\Model\CollectionableInterface;
use Majora\Framework\Model\EntityCollection;
use Majora\Framework\Normalizer\MajoraNormalizer;
use Majora\Framework\Normalizer\Model\NormalizableTrait;

/**
 * Unit test class for MajoraNormalizer.
 */
class MajoraNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function collectionNormalizationCasesProvider()
    {
        return array(
            'scalar_scope' => array('id', array(42, 66)),
            'plain_scope' => array('default', array(
                array('id' => 42, 'color' => 'purple', 'bladesNumber' => 1),
                array('id' => 66, 'color' => 'red', 'bladesNumber' => 3),
            )),
            'embeded_scope' => array('full', array(
                array('id' => 42, 'color' => 'purple', 'bladesNumber' => 1, 'owner' => 'Mace Windu'),
                array('id' => 66, 'color' => 'red', 'bladesNumber' => 3, 'owner' => 'Kylo Ren'),
            )),
        );
    }

    /**
     * Tests EntityCollection normalization.
     *
     * @dataProvider collectionNormalizationCasesProvider
     */
    public function testCollectionNormalization($scope, $expectedNormalization)
    {
        $lightsaber1 = new Lightsaber(42, 1);
        $lightsaber1->color = 'purple';
        $lightsaber1->owner = 'Mace Windu';

        $lightsaber2 = new Lightsaber(66, 3);
        $lightsaber2->color = 'red';
        $lightsaber2->owner = 'Kylo Ren';

        $this->assertEquals(
            $expectedNormalization,
            MajoraNormalizer::createNormalizer()->normalize(
                new LightsaberCollection(array($lightsaber1, $lightsaber2)),
                $scope
            )
        );
    }

    /**
     * Tests denormalize() method through constructor.
     */
    public function testDenormalizeConstruct()
    {
        $expectedLightsaber = new Lightsaber(42, 1);
        $expectedLightsaber->color = 'purple';
        $expectedLightsaber->owner = 'Mace Windu';

        $lightsaber = MajoraNormalizer::createNormalizer()->denormalize(
            array('id' => 42, 'color' => 'purple', 'blades_number' => 1, 'owner' => 'Mace Windu'),
            Lightsaber::class
        );

        $this->assertEquals($expectedLightsaber, $lightsaber);
    }

    /**
     * Tests denormalize() method through constructor with plain object.
     */
    public function testDenormalizeConstructPlainObject()
    {
        $expectedDate = new \DateTime('2016-03-01');

        $date = MajoraNormalizer::createNormalizer()->denormalize(
            '2016-03-01',
            \DateTime::class
        );

        $this->assertEquals($expectedDate, $date);
    }
}

class Lightsaber implements CollectionableInterface
{
    use NormalizableTrait;

    protected $id;
    protected $bladesNumber;
    public $color;
    public $owner;

    public static function getScopes()
    {
        return array(
            'id' => 'id',
            'default' => array('id', 'color', 'bladesNumber'),
            'full' => array('@default', 'owner'),
        );
    }

    /**
     * Construct.
     */
    public function __construct($id, $bladesNumber = null, $nothing = 'wrong')
    {
        $this->id = $id;
        $this->bladesNumber = $bladesNumber;
    }

    /**
     * Returns object id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}

class LightsaberCollection extends EntityCollection
{
    public function getEntityClass()
    {
        return Lightsaber::class;
    }
}
