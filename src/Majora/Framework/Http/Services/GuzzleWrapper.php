<?php

namespace Majora\Framework\Http\Services;

use GuzzleHttp\Client;


class GuzzleWrapper extends Client
{
    protected $clientConfig;

    /**
     * GuzzleWrapper constructor.
     * @param $client
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }
}