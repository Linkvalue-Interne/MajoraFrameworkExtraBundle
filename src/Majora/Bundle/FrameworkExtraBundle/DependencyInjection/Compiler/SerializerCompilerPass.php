<?php

namespace Majora\Bundle\FrameworkExtraBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to guess all serializer formats.
 */
class SerializerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('majora.serializer')) {
            return;
        }

        $serializer  = $container->getDefinition('majora.serializer');
        $handlersDef = $container->findTaggedServiceIds('majora.serialization_handler');

        $handlerReferences = [];
        foreach ($handlersDef as $id => $attributes) {
            foreach ($attributes as $attribute) {
                $handlerReferences[$attribute['format']] = new Reference($id);
            }
        }

        $serializer->replaceArgument(0, $handlerReferences);
    }
}
