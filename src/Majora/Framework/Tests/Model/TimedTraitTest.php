<?php

namespace Majora\Framework\Tests\Model;

use Majora\Framework\Model\TimedTrait;

/**
 * Class TimedTraitTest.
 *
 * @see \Majora\Framework\Model\TimedTrait
 */
class TimedTraitTest extends \PHPUnit_Framework_TestCase
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
        $this->article = new TimedArticle();
    }

    /**
     * Tears down.
     */
    protected function tearDown()
    {
        unset($this->article);
    }

    /**
     * Test TimedTrait::getCreatedAt() method.
     */
    public function testCreatedAtGetter()
    {
        $now = new \DateTime('now');

        $createdAtReflection = new \ReflectionProperty(TimedArticle::class, 'createdAt');
        $createdAtReflection->setAccessible(true);
        $createdAtReflection->setValue($this->article, $now);

        $this->assertEquals($now, $this->article->getCreatedAt());
        $this->assertEquals($now->format('Y-m-d'), $this->article->getCreatedAt('Y-m-d'));
    }

    /**
     * Test TimedTrait::setCreatedAt() method.
     */
    public function testCreatedAtSetter()
    {
        $now = new \DateTime('now');
        $this->article->setCreatedAt($now);

        $createdAtReflection = new \ReflectionProperty(TimedArticle::class, 'createdAt');
        $createdAtReflection->setAccessible(true);

        $this->assertEquals($now, $createdAtReflection->getValue($this->article));
    }

    /**
     * Test TimedTrait::getUpdatedAt() method.
     */
    public function testUpdatedAtGetter()
    {
        $now = new \DateTime('now');

        $updatedAtReflection = new \ReflectionProperty(TimedArticle::class, 'updatedAt');
        $updatedAtReflection->setAccessible(true);
        $updatedAtReflection->setValue($this->article, $now);

        $this->assertEquals($now, $this->article->getUpdatedAt());
        $this->assertEquals($now->format('Y-m-d'), $this->article->getUpdatedAt('Y-m-d'));
    }

    /**
     * Test TimedTrait::setUpdatedAt() method.
     */
    public function testUpdatedAtSetter()
    {
        $now = new \DateTime('now');
        $this->article->setUpdatedAt($now);

        $updatedAtReflection = new \ReflectionProperty(TimedArticle::class, 'updatedAt');
        $updatedAtReflection->setAccessible(true);

        $this->assertEquals($now, $updatedAtReflection->getValue($this->article));
    }
}

/**
 * Class Article
 * Used only for our tests.
 */
class TimedArticle
{
    use TimedTrait;
}
