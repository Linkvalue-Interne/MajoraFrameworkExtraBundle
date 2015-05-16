<?php

namespace Majora\Framework\Serializer\Handler;

/**
 * Interface to implement on format handlers.
 */
interface FormatHandlerInterface
{
    /**
     * serialize given data in handler format using scope.
     *
     * @param mixed  $data
     * @param string $scope
     *
     * @return mixed
     */
    public function serialize($data, $scope);

    /**
     * deserialize given data into output class / type.
     *
     * @param mixed  $data
     * @param string $output
     *
     * @return mixed
     */
    public function deserialize($data, $output);
}
