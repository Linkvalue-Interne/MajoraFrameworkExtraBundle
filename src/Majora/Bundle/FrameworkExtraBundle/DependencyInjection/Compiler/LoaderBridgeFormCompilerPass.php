<?php

namespace Majora\Bundle\FrameworkExtraBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to register bridge loaders into loader form bridge.
 */
class LoaderBridgeFormCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     *
     * Processes "majora.loader_bridge.form" tags
     */
    public function process(ContainerBuilder $container)
    {
        $entityCollectionType = $container->getDefinition('majora.loader.bridge.form.type.entity_collection');
        $loaders = $container->findTaggedServiceIds('majora.loader_bridge.form');

        foreach ($loaders as $loaderId => $tags) {
            foreach ($tags as $attributes) {
                if (!isset($attributes['alias'])) {
                    throw new \RuntimeException('Alias required for "majora.loader_bridge.form" tag.');
                }

                $entityCollectionType->addMethodCall(
                    'registerLoader',
                    [$attributes['alias'], new Reference($loaderId)]
                );
            }
        }
    }
}
