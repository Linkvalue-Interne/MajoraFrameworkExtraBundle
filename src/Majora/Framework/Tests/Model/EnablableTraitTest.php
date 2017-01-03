<?php

namespace Majora\Framework\Tests\Model;

use Majora\Framework\Tests\Model\Fixtures\Article;

/**
 * Class EnablableTraitTest.
 *
 * @see \Majora\Framework\Model\EnablableTrait
 */
class EnablableTraitTest extends \PHPUnit_Framework_TestCase
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
     * Test EnablableTrait::enable() method.
     */
    public function testEnable()
    {
        $enabledReflection = new \ReflectionProperty(Article::class, 'enabled');
        $enabledReflection->setAccessible(true);
        $enabledReflection->setValue($this->article, false);

        $this->article->enable();

        $this->assertTrue($enabledReflection->getValue($this->article));
    }

    /**
     * Test EnablableTrait::disable() method.
     */
    public function testDisable()
    {
        $enabledReflection = new \ReflectionProperty(Article::class, 'enabled');
        $enabledReflection->setAccessible(true);
        $enabledReflection->setValue($this->article, true);

        $this->article->disable();

        $this->assertFalse($enabledReflection->getValue($this->article));
    }

    /**
     * Test EnablableTrait::isEnabled() method.
     */
    public function testIsEnabled()
    {
        $enabledReflection = new \ReflectionProperty(Article::class, 'enabled');
        $enabledReflection->setAccessible(true);

        $enabledReflection->setValue($this->article, true);
        $this->assertTrue($this->article->isEnabled());

        $enabledReflection->setValue($this->article, false);
        $this->assertFalse($this->article->isEnabled());
    }
}
