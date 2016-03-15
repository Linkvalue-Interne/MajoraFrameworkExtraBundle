<?php

namespace Majora\Framework\Loader\Bridge\Api;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Response;
use Majora\Framework\Api\Client\ApiClientInterface;
use Majora\Framework\Loader\LazyLoaderTrait;
use Majora\Framework\Loader\LoaderInterface;
use Majora\Framework\Loader\LoaderTrait;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Trait to use into Api loaders to get a simple implementation of LoaderInterface.
 *
 * @property filterResolver
 */
trait ApiLoaderTrait
{
    use LoaderTrait, LazyLoaderTrait;

    /**
     * @var ApiClientInterface
     */
    protected $restApiClient;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * Construct.
     *
     * @param ApiClientInterface  $restApiClient
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ApiClientInterface $restApiClient,
        SerializerInterface $serializer
    ) {
        $this->restApiClient = $restApiClient;
        $this->serializer = $serializer;
    }

    /**
     * Resolve given filters.
     *
     * @param array $filters
     *
     * @return array
     */
    private function resolveFilters(array $filters = array())
    {
        $this->filterResolver->setDefined('scope');

        return $this->filterResolver->resolve($filters);
    }

    /**
     * Performs an Api call on given method.
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
    ) {
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
            $this->resolveFilters($filters),
            function (Response $response) {
                return $this->serializer
                    ->deserialize(
                        (string) $response->getBody(),
                        $this->collectionClass,
                        'json'
                    )
                    ->map(function ($entity) {
                        return $this->loadDelegates($entity);
                    })
                ;
            },
            new $this->collectionClass()
        );
    }

    /**
     * @see LoaderInterface::retrieveOne()
     */
    public function retrieveOne(array $filters = array())
    {
        return $this->retrieveAll($filters)->first() ?: null;
    }

    /**
     * @see LoaderInterface::retrieve()
     */
    public function retrieve($id)
    {
        return $this->apiCall('get', array('id' => $id), function (Response $response) {
            return $this->loadDelegates(
                $this->serializer->deserialize(
                    (string) $response->getBody(),
                    $this->entityClass,
                    'json'
                )
            );
        });
    }
}
