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
     * {@inheritdoc}
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
                ->arrayNode('translations')
                    ->canBeEnabled()
                    ->children()
                        ->arrayNode('locales')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('agnostic_url_generator')
                    ->canBeEnabled()
                ->end()
                ->arrayNode('exception_listener')
                    ->canBeEnabled()
                ->end()
                ->arrayNode('doctrine_events_proxy')
                    ->canBeEnabled()
                ->end()
                ->arrayNode('json_form_extension')
                    ->canBeEnabled()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
