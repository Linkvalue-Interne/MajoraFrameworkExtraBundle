<?php

namespace Majora\Framework\Tests\Model;

use Majora\Framework\Tests\Model\Fixtures\Article;
use Majora\Framework\Tests\Model\Fixtures\ArticleCollection;

/**
 * Class TranslatableCollectionTraitTest.
 *
 * @see \Majora\Framework\Model\TranslatableCollectionTrait
 */
class TranslatableCollectionTraitTest extends \PHPUnit_Framework_TestCase
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
     * Test TranslatableCollectionTrait::setLocale() method.
     */
    public function testLocaleSetter()
    {
        $this->articleCollection->setLocale('fr');

        foreach ($this->articleCollection as $article) {
            $localeReflection = new \ReflectionProperty(Article::class, 'locale');
            $localeReflection->setAccessible(true);

            $this->assertEquals('fr', $localeReflection->getValue($article));
        }
    }

    /**
     * Test TranslatableCollectionTrait::setDefaultLocale() method.
     */
    public function testDefaultLocaleSetter()
    {
        $this->articleCollection->setDefaultLocale('en');

        foreach ($this->articleCollection as $article) {
            $defaultLocaleReflection = new \ReflectionProperty(Article::class, 'defaultLocale');
            $defaultLocaleReflection->setAccessible(true);

            $this->assertEquals('en', $defaultLocaleReflection->getValue($article));
        }
    }
}
