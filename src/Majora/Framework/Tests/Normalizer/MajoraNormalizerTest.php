<?php

namespace Majora\Framework\Tests\Normalizer;

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
        return [
            'scalar_scope' => ['id', [42, 66]],
            'plain_scope' => [
                'default',
                [
                    ['id' => 42, 'color' => 'purple', 'bladesNumber' => 1],
                    ['id' => 66, 'color' => 'red', 'bladesNumber' => 3],
                ],
            ],
            'embeded_scope' => [
                'full',
                [
                    ['id' => 42, 'color' => 'purple', 'bladesNumber' => 1, 'owner' => 'Mace Windu'],
                    ['id' => 66, 'color' => 'red', 'bladesNumber' => 3, 'owner' => 'Kylo Ren'],
                ],
            ],
        ];
    }

    /**
     * Tests EntityCollection normalization.
     *
     * @dataProvider collectionNormalizationCasesProvider
     */
    public function testCollectionNormalization($scope, $expectedNormalization)
    {
        $lightsaber1 = new Lightsaber(42, 1);
        $lightsaber1->setColor('purple');
        $lightsaber1->owner = 'Mace Windu';

        $lightsaber2 = new Lightsaber(66, 3);
        $lightsaber2->setColor('red');
        $lightsaber2->owner = 'Kylo Ren';

        $this->assertEquals(
            $expectedNormalization,
            MajoraNormalizer::createNormalizer()->normalize(
                new LightsaberCollection([$lightsaber1, $lightsaber2]),
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
        $expectedLightsaber->setColor('purple');
        $expectedLightsaber->owner = 'Mace Windu';

        $lightsaber = MajoraNormalizer::createNormalizer()->denormalize(
            ['id' => 42, 'color' => 'purple', 'blades_number' => 1, 'owner' => 'Mace Windu'],
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

/**
 * Class Lightsaber
 * Only used for our tests.
 */
class Lightsaber implements CollectionableInterface
{
    use NormalizableTrait;

    protected $id;

    protected $bladesNumber;

    protected $color;

    public $owner;

    public static function getScopes()
    {
        return [
            'id' => 'id',
            'default' => ['id', 'color', 'bladesNumber'],
            'full' => ['@default', 'owner'],
        ];
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

    /**
     * @param $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }
}

/**
 * Class LightsaberCollection
 * Only used for our tests.
 */
class LightsaberCollection extends EntityCollection
{
    /**
     * @return string
     */
    public function getEntityClass()
    {
        return Lightsaber::class;
    }
}
