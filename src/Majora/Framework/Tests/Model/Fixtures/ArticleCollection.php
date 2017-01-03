<?php

namespace Majora\Framework\Tests\Model\Fixtures;

use Majora\Framework\Model\EntityCollection;
use Majora\Framework\Model\TranslatableCollectionTrait;

/**
 * Class ArticleCollection
 *
 * Used only for our tests.
 */
class ArticleCollection extends EntityCollection
{
    use TranslatableCollectionTrait;

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return Article::class;
    }
}
