<?php

namespace Majora\Framework\Tests\Model;

use Majora\Framework\Tests\Model\Fixtures\Article;
use Majora\Framework\Tests\Model\Fixtures\TagCollection;

/**
 * Class CollectionableTraitTest.
 *
 * @see \Majora\Framework\Model\CollectionableTrait
 */
class CollectionableTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test CollectionableTrait::toCollection() method.
     */
    public function testToCollection()
    {
        $article = new Article(
            'Article test',
            [
                'tag1',
                'tag2',
                'tag3',
            ]
        );

        $tagReflection = new \ReflectionProperty(Article::class, 'tags');
        $tagReflection->setAccessible(true);

        $this->assertInstanceOf(TagCollection::class, $tagReflection->getValue($article));
    }

    /**
     * Test CollectionableTrait::toCollection() method with collection.
     */
    public function testToCollectionWithCollection()
    {
        $collection = new TagCollection(
            [
                'tag1',
                'tag2',
                'tag3',
            ]
        );
        $article = new Article('Article test', $collection);

        $tagReflection = new \ReflectionProperty(Article::class, 'tags');
        $tagReflection->setAccessible(true);

        $this->assertEquals($collection, $tagReflection->getValue($article));
    }

    /**
     * Test CollectionableTrait::toCollection() method throw exception.
     */
    public function testToCollectionException()
    {
        $this->expectException(\InvalidArgumentException::class);

        new Article('Article test', 'My tag');
    }
}
