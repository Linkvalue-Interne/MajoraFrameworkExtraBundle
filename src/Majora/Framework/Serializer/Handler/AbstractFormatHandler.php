<?php

namespace Majora\Framework\Serializer\Handler;

use Majora\Framework\Normalizer\MajoraNormalizer;
use Majora\Framework\Serializer\Handler\FormatHandlerInterface;

/**
 * Base implementation of format handler using normalizer
 */
abstract class AbstractFormatHandler implements FormatHandlerInterface
{
    /**
     * @var MajoraNormalizer
     */
    protected $normalizer;

    /**
     * Construct
     *
     * @param MajoraNormalizer $normalizer
     */
    public function __construct(MajoraNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }
}
