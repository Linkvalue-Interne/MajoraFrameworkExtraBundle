<?php

namespace Majora\Framework\Loader\Bridge\Api;

use Majora\Framework\Api\Client\RestApiClient;
use Majora\Framework\Loader\LoaderInterface;
use Majora\Framework\Loader\LoaderTrait;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Trait to use into Api loaders to get a simple implementation of LoaderInterface
 */
trait ApiLoaderTrait
{
    use LoaderTrait;

    /**
     * @var RestApiClient
     */
    protected $restApiClient;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * Construct
     *
     * @param RestApiClient $restApiClient
     * @param SerializerInterface $serializer
     */
    public function __construct(
        RestApiClient $restApiClient,
        SerializerInterface $serializer
    )
    {
        $this->restApiClient = $restApiClient;
        $this->serializer = $serializer;
    }

    /**
     * @see LoaderInterface::retrieveAll()
     */
    public function retrieveAll(array $filters = array(), $limit = null, $offset = null)
    {
        $this->assertIsConfigured();

        return $this->serializer->deserialize(
            $this->restApiClient->cget(
                $this->filterResolver->resolve($filters)
            ),
            $this->collectionClass,
            'json'
        );
    }

    /**
     * @see LoaderInterface::retrieveOne()
     */
    public function retrieveOne(array $filters = array())
    {
        $this->assertIsConfigured();

        return $this->serializer->deserialize(
            $this->restApiClient->get($filters),
            $this->entityClass,
            'json'
        );
    }

    /**
     * @see LoaderInterface::retrieve()
     */
    public function retrieve($id)
    {
        $this->assertIsConfigured();

        return $this->retrieveOne(array('id' => $id));
    }
}
