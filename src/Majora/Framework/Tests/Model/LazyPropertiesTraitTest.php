<?php

namespace Majora\Framework\Tests\Model;

use Majora\Framework\Model\EntityCollection;
use Majora\Framework\Tests\Model\Fixtures\Article;
use Majora\Framework\Tests\Model\Fixtures\Category;

/**
 * Class LazyPropertiesTraitTest.
 *
 * @see \Majora\Framework\Model\LazyPropertiesTrait
 */
class LazyPropertiesTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Article
     */
    private $article;

    /**
     * Sets up.
     */
    protected function setUp()
    {
        $this->article = new Article("Article 1");
    }

    /**
     * Tears down.
     */
    protected function tearDown()
    {
        unset($this->article);
    }

    /**
     * Test LazyPropertiesTrait::registerLoader() method.
     */
    public function testRegisterLoader()
    {
        $delegatesCollectionReflection = new \ReflectionProperty(Article::class, 'delegatesCollection');
        $delegatesCollectionReflection->setAccessible(true);

        $categoryClosure = function () {
            return new Category('News');
        };

        $this->article->registerLoader('category', $categoryClosure);

        $delegatesCollection = $delegatesCollectionReflection->getValue($this->article);

        $this->assertInstanceOf(EntityCollection::class, $delegatesCollection);
        $this->assertEquals($categoryClosure, $delegatesCollection->get('category'));
    }

    /**
     * Test LazyPropertiesTrait::registerLoaders() method.
     */
    public function testRegisterLoaders()
    {
        $delegatesCollectionReflection = new \ReflectionProperty(Article::class, 'delegatesCollection');
        $delegatesCollectionReflection->setAccessible(true);

        $closures = [
            'category' => function () {
                return new Category('News');
            },
        ];

        $this->article->registerLoaders($closures);
        $delegatesCollection = $delegatesCollectionReflection->getValue($this->article);

        $this->assertEquals($closures['category'], $delegatesCollection->get('category'));
    }

    /**
     * Test LazyPropertiesTrait::load() method.
     */
    public function testLoad()
    {
        $loadReflection = new \ReflectionMethod(Article::class, 'load');
        $loadReflection->setAccessible(true);

        $this->assertEquals($this->article->title, $loadReflection->invokeArgs($this->article, ['title']));
        $this->assertNull($loadReflection->invokeArgs($this->article, ['category']));

        $category = new Category('News');

        $this->article->registerLoader(
            'category',
            function () use ($category) {
                return $category;
            }
        );

        $this->assertEquals($category, $loadReflection->invokeArgs($this->article, ['category']));
    }
}
