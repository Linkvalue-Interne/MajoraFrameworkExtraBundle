<?php

namespace Majora\Framework\Tests\Model;

use Majora\Framework\Model\EntityCollection;
use Majora\Framework\Tests\Model\Fixtures\Article;
use Majora\Framework\Tests\Model\Fixtures\ArticleCollection;
use Majora\Framework\Tests\Model\Fixtures\TagCollection;

/**
 * Class EntityCollectionTest.
 *
 * @see \Majora\Framework\Model\EntityCollection
 */
class EntityCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ArticleCollection
     */
    private $articleCollection;

    /**
     * Sets up.
     */
    protected function setUp()
    {
        $this->articleCollection = new ArticleCollection();

        for ($i = 1; $i <= 3; $i++) {
            $article = new Article();
            $article->title = "Article $i";

            $this->articleCollection->add($article);
        }
    }

    /**
     * Tears down.
     */
    protected function tearDown()
    {
        unset($this->articleCollection);
    }

    /**
     * Test EntityCollection::getScopes() method.
     */
    public function testScopesGetter()
    {
        $this->assertTrue(is_array(ArticleCollection::getScopes()));
    }

    /**
     * Test EntityCollection::getEntityClass() method exception.
     */
    public function testEntityClassGetterException()
    {
        $this->expectException(\BadMethodCallException::class);

        $tags = new TagCollection();
        $tags->getEntityClass();
    }

    /**
     * Test EntityCollection::normalize() method.
     */
    public function testNormalize()
    {
        $this->assertEquals(
            [
                ['title' => 'Article 1'],
                ['title' => 'Article 2'],
                ['title' => 'Article 3'],
            ],
            $this->articleCollection->normalize('default')
        );
    }

    /**
     * Test EntityCollection::denormalize() method.
     */
    public function testDenormalize()
    {
        $collection = new ArticleCollection();
        $collection->denormalize(
            [
                ['title' => 'Article 1'],
                ['title' => 'Article 2'],
                ['title' => 'Article 3'],
            ]
        );

        $this->assertEquals($this->articleCollection, $collection);
    }

    /**
     * Test EntityCollection::serialize() method.
     */
    public function testLegacySerialize()
    {
        $collection = $this
            ->getMockBuilder(EntityCollection::class)
            ->setMethodsExcept(['serialize'])
            ->getMock();

        $scope = 'my_scope';

        $collection
            ->expects($this->once())
            ->method('normalize')
            ->with($scope);

        $collection->serialize($scope);
    }

    /**
     * Test EntityCollection::deserialize() method.
     */
    public function testLegacyDeserialize()
    {
        $collection = $this
            ->getMockBuilder(EntityCollection::class)
            ->setMethodsExcept(['deserialize'])
            ->getMock();

        $data = [
            'test1' => 'value1',
            'test2' => 'value2',
            'test3' => 'value3',
        ];

        $collection
            ->expects($this->once())
            ->method('denormalize')
            ->with($data);

        $collection->deserialize($data);
    }

    /**
     * Test EntityCollection::search() method.
     */
    public function testSearch()
    {
    }

    /**
     * Test EntityCollection::chunk() method.
     */
    public function testChunk()
    {
    }

    /**
     * Test EntityCollection::cslice() method.
     */
    public function testCslice()
    {
    }

    /**
     * Test EntityCollection::indexBy() method.
     */
    public function testIndexBy()
    {
    }

    /**
     * Test EntityCollection::sort() method.
     */
    public function testSort()
    {
    }

    /**
     * Test EntityCollection::reduce() method.
     */
    public function testReduce()
    {
    }
}
