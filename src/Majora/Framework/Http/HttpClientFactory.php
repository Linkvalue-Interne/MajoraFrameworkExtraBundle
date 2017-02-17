<?php

namespace Majora\Framework\Http;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Yaml\Parser;



class HttpClientFactory
{
    public function __construct($container, $config)
    {
        $this->config = $config;
        $this->container = $container;
        $this->init();
    }


    public function init()
    {
        $parser = new Parser();
        if(!empty($this->config['path'])) {
            $clients = $parser->parse(file_get_contents($this->container->getParameter('kernel.root_dir').$this->config['path']));
        }
        else {
            $clients = $parser->parse(file_get_contents($this->container->getParameter('kernel.root_dir').'/majora_client.yml'));
        }

        foreach ($clients as $clientId => $clientConfig)
        {
            $this->createClient($clientConfig, $clientId);
        }
    }

    public function createClient($clientConfig, $clientId){

        $this->container->setDefinition(sprintf('majora_http.handler.%s', $clientId), $this->container->getDefinition('guzzle.curl_handler'));
        $handlerStackReference = new Reference(sprintf('majora_http.handler.%s', $clientId));

        $this->container->getDefinition(sprintf('majora_http.handler.%s', $clientId));

        //Middleware
        $eventDispatcher = $this->container->getDefinition('majora.http_eventdispatcher');
        $eventDispatcher->replaceArgument(2, $clientId);
        $eventDispatcher->addMethodCall('push', [$handlerStackReference]);

        $clientConfig['handler'] = $handlerStackReference;
        $clientConfig['middleware'] = $eventDispatcher;
        $guzzleClient= $this->container->getDefinition('guzzle_wrapper');
        $guzzleClient->replaceArgument(0, $clientConfig);
        $this->container->setDefinition(sprintf('guzzle_http.%s', $clientId) , $guzzleClient);
    }
}



