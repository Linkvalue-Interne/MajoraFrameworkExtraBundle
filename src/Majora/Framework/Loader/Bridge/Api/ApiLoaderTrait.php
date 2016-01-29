<?php

namespace Majora\Framework\Loader\Bridge\Api;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Response;
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
     * Performs an Api call on given method
     *
     * @param string   $method
     * @param array    $query
     * @param callable $onSuccess
     * @param mixed    $emptyValue
     */
    private function apiCall(
        $method,
        array $query = array(),
        callable $onSuccess,
        $emptyValue = null
    )
    {
        try {
            return $onSuccess(
                $this->restApiClient->$method($query)
            );
        } catch (BadResponseException $e) {
            if (($response = $e->getResponse())
                && $response->getStatusCode() == 404
            ) {
                return $emptyValue;
            }

            throw $e;
        }
    }

    /**
     * @see LoaderInterface::retrieveAll()
     */
    public function retrieveAll(array $filters = array(), $limit = null, $offset = null)
    {
        return $this->apiCall(
            'cget',
            $this->filterResolver->resolve($filters),
            function(Response $response) {
                return $this->serializer->deserialize(
                    (string) $response->getBody(),
                    $this->collectionClass,
                    'json'
                );
            },
            new $this->collectionClass()
        );
    }

    /**
     * @see LoaderInterface::retrieveOne()
     */
    public function retrieveOne(array $filters = array())
    {
        return $this->apiCall(
            'get',
            $this->filterResolver->resolve($filters),
            function(Response $response) {
                return $this->serializer->deserialize(
                    (string) $response->getBody(),
                    $this->entityClass,
                    'json'
                );
            }
        );
    }

    /**
     * @see LoaderInterface::retrieve()
     */
    public function retrieve($id)
    {
        return $this->retrieveOne(array('id' => $id));
    }
}
