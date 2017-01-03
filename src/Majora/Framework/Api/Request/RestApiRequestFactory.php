<?php

namespace Majora\Framework\Api\Request;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Request factory
 */
class RestApiRequestFactory
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var array
     */
    protected $routeMapping;

    /**
     * @var array
     */
    protected $defaultOptions;

    /**
     * @var array
     */
    protected $defaultBodyData;

    /**
     * Construct
     *
     * @param UrlGeneratorInterface $urlGenerator
     * @param array                 $defaultOptions
     * @param array                 $defaultBodyData
     * @param array                 $routeMapping
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        array $defaultOptions = [],
        array $defaultBodyData = [],
        array $routeMapping = []
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->defaultOptions = $defaultOptions;
        $this->defaultBodyData = $defaultBodyData;
        $this->routeMapping = $routeMapping;
    }

    /**
     * Register a new route mapping
     *
     * @param array $routeMapping
     */
    public function registerRouteMapping(array $routeMapping)
    {
        $this->routeMapping = $routeMapping;
    }

    /**
     * Create a http request uri from presets under $name parameter
     *
     * @param  string $name
     * @param  array  $query
     *
     * @return string
     *
     * @throws \InvalidArgumentException If request does not exist for given name
     */
    public function createRequestUri($name, array $query = [])
    {
        if (empty($this->routeMapping[$name])) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Unknow request preset under name "%s", only [%s] defined.',
                    $name,
                    implode('","', array_keys($this->routeMapping))
                )
            );
        }

        // build request
        return $this->urlGenerator->generate(
            $this->routeMapping[$name],
            $query,
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * Create request body data
     *
     * @param array $body
     *
     * @return array
     */
    public function createRequestBodyData(array $body = [])
    {
        return array_replace_recursive(
            $this->defaultBodyData,
            $body
        );
    }

    /**
     * Create request options, flatten default one and given one
     *
     * @param array $options
     *
     * @return array
     */
    public function createRequestOptions(array $options = [])
    {
        return array_replace_recursive(
            $this->defaultOptions,
            $options
        );
    }
}
