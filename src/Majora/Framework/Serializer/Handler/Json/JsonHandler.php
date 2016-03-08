<?php

namespace Majora\Framework\Serializer\Handler\Json;

use Majora\Framework\Normalizer\Model\NormalizableInterface;
use Majora\Framework\Serializer\Handler\AbstractFormatHandler;
use Majora\Framework\Serializer\Handler\Json\Exception\JsonDeserializationException;

/**
 * Handler implementation creating and using json.
 */
class JsonHandler extends AbstractFormatHandler
{
    /**
     * @see FormatHandlerInterface::serialize()
     */
    public function serialize($data, $scope)
    {
        return json_encode($data instanceof NormalizableInterface ?
            $this->normalizer->normalize($data, $scope) :
            $data
        );
    }

    /**
     * @see FormatHandlerInterface::deserialize()
     */
    public function deserialize($data, $output)
    {
        $arrayData = json_decode($data, true);
        if (null === $arrayData) {
            throw new JsonDeserializationException(sprintf(
                'Invalid json data, error %s : %s',
                json_last_error(),
                function_exists('json_last_error_msg') ? // php 5.4 compatibility
                    json_last_error_msg() : 'error message unavailable'
            ));
        }

        return $this->normalizer->denormalize($arrayData, $output);
    }
}
