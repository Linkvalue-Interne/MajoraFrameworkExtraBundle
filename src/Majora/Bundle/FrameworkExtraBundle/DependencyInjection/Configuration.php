<?php

namespace Majora\Bundle\FrameworkExtraBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('majora_framework_extra')
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('clock')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('mock_param')
                            ->defaultValue('_date_mock')
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('agnostic_url_generator')
                    ->canBeEnabled()
                ->end()
                ->arrayNode('web_socket')
                    ->children()
                        ->arrayNode('server')
                            ->canBeEnabled()
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('protocol')
                                    ->defaultValue('tcp')
                                    ->cannotBeEmpty()
                                ->end()
                                ->scalarNode('host')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('client')
                            ->canBeEnabled()
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('remote_protocol')
                                    ->defaultValue('tcp')
                                    ->cannotBeEmpty()
                                ->end()
                                ->scalarNode('remote_host')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->arrayNode('listen')
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
