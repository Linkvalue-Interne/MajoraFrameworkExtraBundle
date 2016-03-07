<?php

namespace Majora\Framework\Domain\Action\Api;

use Majora\Framework\Api\Client\RestApiClient;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Base trait for api actions
 */
trait ApiActionTrait
{
    /**
     * @var RestApiClient
     */
    private $restClient;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * define rest client.
     *
     * @param RestApiClient $restClient
     */
    public function setRestApiClient(RestApiClient $restClient)
    {
        $this->restClient = $restClient;
    }

    /**
     * returns api client if defined
     *
     * @return RestApiClient
     */
    protected function getRestApiClient()
    {
        if (!$this->restClient) {
            throw new \BadMethodCallException(sprintf(
                'Method %s() cannot be used while rest api client is not configured.',
                __METHOD__
            ));
        }

        return $this->restClient;
    }

    /**
     * define serializer.
     *
     * @param SerializerInterface $serializer
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * returns serializer if defined
     *
     * @return SerializerInterface
     */
    protected function getSerializer()
    {
        if (!$this->serializer) {
            throw new \BadMethodCallException(sprintf(
                'Method %s() cannot be used while serializer is not configured.',
                __METHOD__
            ));
        }

        return $this->serializer;
    }
}
