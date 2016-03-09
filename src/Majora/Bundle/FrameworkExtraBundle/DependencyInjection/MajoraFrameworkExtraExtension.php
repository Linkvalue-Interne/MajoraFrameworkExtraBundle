<?php

namespace Majora\Bundle\FrameworkExtraBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
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
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('serializer.xml');
        $loader->load('http.xml');
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

        // json form extension
        if (!empty($config['json_form_extension']['enabled'])) {
            $loader->load('json_form_extension.xml');
        }
    }
}
