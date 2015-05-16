<?php

namespace Majora\Framework\Serializer\Handler\Collection;

use Majora\Framework\Serializer\Handler\FormatHandlerInterface;
use Majora\Framework\Serializer\Model\SerializableInterface;

/**
 * Handler implementation creating and using arrays.
 */
class CollectionHandler
    implements FormatHandlerInterface
{
    /**
     * @see FormatHandlerInterface::serialize()
     */
    public function serialize($data, $scope)
    {
        return $data instanceof SerializableInterface ?
            $data->serialize($scope) :
            (array) $data
        ;
    }

    /**
     * @see FormatHandlerInterface::deserialize()
     */
    public function deserialize($data, $output)
    {
        if (!class_exists($output)) {
            return $data;
        }

        $object = new $output();

        return $object instanceof SerializableInterface ?
            $object->deserialize($data) :
            $object
        ;
    }
}
