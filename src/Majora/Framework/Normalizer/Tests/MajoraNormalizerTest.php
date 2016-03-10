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
        $lightsaber1 = new Lightsaber();
        $lightsaber1->id = 42;
        $lightsaber1->color = 'purple';
        $lightsaber1->bladesNumber = 1;
        $lightsaber1->owner = 'Mace Windu';

        $lightsaber2 = new Lightsaber();
        $lightsaber2->id = 66;
        $lightsaber2->color = 'red';
        $lightsaber2->bladesNumber = 3;
        $lightsaber2->owner = 'Kylo Ren';

        $this->assertEquals(
            $expectedNormalization,
            MajoraNormalizer::createNormalizer()->normalize(
                new LightsaberCollection(array($lightsaber1, $lightsaber2)),
                $scope
            )
        );
    }
}

class Lightsaber implements CollectionableInterface
{
    use NormalizableTrait;

    public $id;
    public $color;
    public $bladesNumber;
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
