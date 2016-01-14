<?php

namespace Majora\Framework\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration class used for majora related bundles.
 */
abstract class MajoraConfiguration implements ConfigurationInterface
{
    protected $handledPersistences = array('default');
    protected $handledDomains      = array('default');
    protected $handledLoaders      = array('default');

    /**
     * create and return node section for majora entities.
     *
     * @param string $entity
     *
     * @return ArrayNodeDefinition
     */
    protected function createEntitySection($entity)
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root($entity);

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->enumNode('persistence')
                    ->cannotBeEmpty()
                    ->defaultValue('fixtures')
                    ->values($this->handledPersistences)
                ->end()
                ->enumNode('domain')
                    ->cannotBeEmpty()
                    ->defaultValue('default')
                    ->values($this->handledDomains)
                ->end()
                ->enumNode('loader')
                    ->cannotBeEmpty()
                    ->defaultValue('default')
                    ->values($this->handledLoaders)
                ->end()
            ->end()
        ;

        return $node;
    }
}
