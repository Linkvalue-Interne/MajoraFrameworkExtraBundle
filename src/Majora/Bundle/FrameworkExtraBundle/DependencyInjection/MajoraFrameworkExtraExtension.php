<?php

namespace Majora\Bundle\FrameworkExtraBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MajoraFrameworkExtraExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('serializer.xml');
        $loader->load('services.xml');

        // clock mocker
        if (!empty($config['clock']['enabled'])) {
            $loader->load('clock.xml');
            $container->getDefinition('majora.clock')->replaceArgument(
                0,
                $config['clock']['mock_param']
            );
        }

        // translations
        if (!empty($config['translations']['enabled'])) {
            $container->setParameter(
                'majora.translations.enabled_locales',
                $config['translations']['locales']
            );
            $loader->load('translations.xml');
        }

        // agnostic url generator
        if (!empty($config['agnostic_url_generator']['enabled'])) {
            $loader->load('agnostic_url_generator.xml');
        }

        // exception listener
        if (!empty($config['exception_listener']['enabled'])) {
            $loader->load('exception_listener.xml');
        }

        // doctrine events proxy
        if (!empty($config['doctrine_events_proxy']['enabled'])) {
            $loader->load('doctrine_events_proxy.xml');
        }

        // web socket server
        if (!empty($config['web_socket']['server']['enabled'])) {
            $container->setParameter('majora.web_socket.server.end_point', sprintf(
                '%s://%s',
                $config['web_socket']['server']['protocol'],
                $config['web_socket']['server']['host']
            ));

            $loader->load('web_socket_server.xml');
        }

        // web socket client
        if (!empty($config['web_socket']['client']['type'])) {
            $wsType = $config['web_socket']['client']['type'];

            if (empty($config['web_socket']['client'][$wsType])) {
                throw new \InvalidArgumentException(sprintf(
                    'You have to provide "%s" configuration key under "majora_framework_extra.web_socket.client.%s" configuration key.',
                    $wsType,
                    $wsType
                ));
            }

            $loader->load('web_socket_client.xml');
            $parameters = array(
                'majora.web_socket.client.remote_end_point' => '',
                'majora.web_socket.client.route' => '',
                'majora.web_socket.client.arguments' => '',
            );

            switch ($wsType) {
                case 'hoa' :
                    $parameters['majora.web_socket.client.remote_end_point'] = sprintf(
                        '%s://%s',
                        $config['web_socket']['client']['hoa']['protocol'],
                        $config['web_socket']['client']['hoa']['host']
                    );

                    $webSocketClientId = 'majora.web_socket.hoa_client';

                break;

                case 'api' :
                    $parameters['majora.web_socket.client.arguments'] =
                        $config['web_socket']['client']['api']['arguments']
                    ;

                    $webSocketClientId = 'majora.web_socket.api_client';
                    $webSocketClientDefinition = $container->getDefinition($webSocketClientId);
                    $webSocketClientDefinition->addTag('majora.agnostic_route', array(
                        'injector' => 'setWsApiEndpoint',
                        'route' => $config['web_socket']['client']['api']['route'],
                        'arguments' => 'majora.web_socket.client.arguments',
                    ));

                break;
            }

            foreach ($parameters as $key => $value) {
                $container->setParameter($key, $value);
            }

            $container->setAlias('majora.web_socket.client', new Alias($webSocketClientId));
            $webSocketWrapperDefinition = $container->getDefinition('majora.web_socket.event_listener');

            foreach ($config['web_socket']['client']['listen'] as $listenedEvent) {
                $webSocketWrapperDefinition->addTag('kernel.event_listener', array(
                    'event' => $listenedEvent,
                    'method' => 'onBroadcastableEvent',
                ));
            }
        }
    }
}
