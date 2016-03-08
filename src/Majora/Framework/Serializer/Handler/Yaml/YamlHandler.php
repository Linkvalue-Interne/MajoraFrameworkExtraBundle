<?php

namespace Majora\Framework\Serializer\Handler\Yaml;

use Majora\Framework\Normalizer\MajoraNormalizer;
use Majora\Framework\Serializer\Handler\AbstractFormatHandler;
use Symfony\Component\Yaml\Yaml;

/**
 * Handler implementation creating and using yaml.
 */
class YamlHandler extends AbstractFormatHandler
{
    /**
     * @var Yaml
     */
    protected $yamlParser;

    /**
     * construct.
     *
     * @param Yaml             $yamlParser
     * @param MajoraNormalizer $normalizer
     */
    public function __construct(Yaml $yamlParser, MajoraNormalizer $normalizer)
    {
        $this->yamlParser = $yamlParser;

        parent::__construct($normalizer);
    }

    /**
     * @see FormatHandlerInterface::serialize()
     */
    public function serialize($data, $scope)
    {
        return $this->yamlParser->dump(
            $this->normalizer->normalize($data, $scope)
        );
    }

    /**
     * @see FormatHandlerInterface::deserialize()
     */
    public function deserialize($data, $output)
    {
        return $this->normalizer->denormalize(
            $this->yamlParser->parse($data),
            $output
        );
    }
}
