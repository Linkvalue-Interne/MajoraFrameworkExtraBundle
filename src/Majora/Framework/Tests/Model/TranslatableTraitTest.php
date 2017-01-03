<?php

namespace Majora\Framework\Tests\Model;

use Majora\Framework\Tests\Model\Fixtures\Article;

/**
 * Class TranslatableTraitTest.
 *
 * @see \Majora\Framework\Model\TranslatableTrait
 */
class TranslatableTraitTest extends \PHPUnit_Framework_TestCase
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
        $this->article = new Article();
    }

    /**
     * Tears down.
     */
    protected function tearDown()
    {
        unset($this->article);
    }

    /**
     * Test TranslatableTrait::setLocale() method.
     */
    public function testLocaleSetter()
    {
        $this->article->setLocale('fr');

        $localeReflection = new \ReflectionProperty(Article::class, 'locale');
        $localeReflection->setAccessible(true);

        $this->assertEquals('fr', $localeReflection->getValue($this->article));
    }

    /**
     * Test TranslatableTrait::setDefaultLocale() method.
     */
    public function testDefaultLocaleSetter()
    {
        $this->article->setDefaultLocale('en');

        $defaultLocaleReflection = new \ReflectionProperty(Article::class, 'defaultLocale');
        $defaultLocaleReflection->setAccessible(true);

        $this->assertEquals('en', $defaultLocaleReflection->getValue($this->article));
    }

    /**
     * Test TranslatableTrait::getTranslation() method.
     */
    public function testTranslationGetter()
    {
        $this->article->setLocale('fr');
        $this->article->setDefaultLocale('en');

        $data = [
            'fr' => 'Mon message en français.',
            'en' => 'My message in english.',
        ];

        $default = 'My default message.';

        $getTranslationReflection = new \ReflectionMethod(Article::class, 'getTranslation');
        $getTranslationReflection->setAccessible(true);

        $this->assertEquals($default, $getTranslationReflection->invokeArgs($this->article, [[], $default]));
        $this->assertEquals($data['fr'], $getTranslationReflection->invokeArgs($this->article, [$data, $default]));

        unset($data['fr']);

        $this->assertEquals($data['en'], $getTranslationReflection->invokeArgs($this->article, [$data, $default]));
    }
}
