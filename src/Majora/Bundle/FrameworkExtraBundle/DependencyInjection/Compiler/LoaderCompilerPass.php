<?php

namespace Majora\Bundle\FrameworkExtraBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to register loaders setUp
 */
class LoaderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $loaderTags = $container->findTaggedServiceIds('majora.loader');
        $doctrineProxy = $container->hasDefinition('majora.doctrine.event_proxy') ?
            $container->getDefinition('majora.doctrine.event_proxy') :
            null
        ;
        $registerProxy = (boolean) $doctrineProxy;

        foreach ($loaderTags as $loaderId => $tags) {
            $loaderDefinition = $container->getDefinition($loaderId);
            $reflection = new \ReflectionClass($loaderDefinition->getClass());
            $setUp = $reflection->hasMethod('setUp');

            foreach ($tags as $attributes) {
                if ($setUp) {
                    $loaderDefinition->addMethodCall('setUp', array(
                        new Reference($attributes['repository']),
                        $attributes['entityClass'],
                        $attributes['entityCollection']
                    ));
                }
                if ($registerProxy) {
                    $doctrineProxy->addMethodCall('registerDoctrineLoader', array(
                        $attributes['entityClass'],
                        $loaderId
                    ));
                }
            }
        }
    }
}
