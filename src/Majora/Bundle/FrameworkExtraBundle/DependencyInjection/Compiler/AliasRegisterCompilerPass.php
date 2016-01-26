<?php

namespace Majora\Bundle\FrameworkExtraBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to register extra aliases from services
 */
class AliasRegisterCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $aliasTags = $container->findTaggedServiceIds('majora.alias');

        foreach ($aliasTags as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                $container->setAlias($attributes['alias'], $serviceId);
            }
        }
    }
}
