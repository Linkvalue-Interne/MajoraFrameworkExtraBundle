<?php

namespace Majora\Framework\Tests\Model\Fixtures;

use Majora\Framework\Model\CollectionableTrait;
use Majora\Framework\Model\EnablableTrait;
use Majora\Framework\Model\LazyPropertiesTrait;
use Majora\Framework\Model\TranslatableInterface;
use Majora\Framework\Model\TranslatableTrait;
use Majora\Framework\Normalizer\Model\NormalizableInterface;
use Majora\Framework\Normalizer\Model\NormalizableTrait;

/**
 * Class Article
 *
 * Used only for our tests.
 */
class Article implements TranslatableInterface, NormalizableInterface
{
    use TranslatableTrait;

    use CollectionableTrait;

    use EnablableTrait;

    use LazyPropertiesTrait;

    use NormalizableTrait;

    /**
     * @var string
     */
    public $title;

    /**
     * @var Category
     */
    public $category;

    /**
     * @var TagCollection
     */
    private $tags;

    /**
     * Article constructor.
     *
     * @param null|string $title
     * @param array       $tags
     */
    public function __construct($title = null, $tags = [])
    {
        $this->title = $title;
        $this->tags = $this->toCollection($tags, TagCollection::class);
    }

    /**
     * @return array
     */
    public static function getScopes()
    {
        return [
            'default' => ['title'],
            'full' => ['title', 'tags'],
        ];
    }
}
