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

        foreach ($loaderTags as $loaderId => $tags) {
            $loaderDefinition = $container->getDefinition($loaderId);
            $loaderReflection = new \ReflectionClass($loaderDefinition->getClass());
            $setUp = $loaderReflection->hasMethod('setUp');

            foreach ($tags as $attributes) {
                if ($setUp) {
                    $entityReflection = new \ReflectionClass($attributes['entityClass']);
                    if (!$entityReflection->implementsInterface('Majora\Framework\Model\CollectionableInterface')) {
                        throw new \InvalidArgumentException(sprintf(
                            'Cannot support "%s" class into "%s" : managed items have to be Majora\Framework\Model\CollectionableInterface.',
                            $entityClass,
                            $loaderDefinition->getClass()
                        ));
                    }

                    $loaderDefinition->addMethodCall('setUp', array(
                        $attributes['entityClass'],
                        array_map(
                            function($property) { return $property->getName(); },
                            $entityReflection->getProperties()
                        ),
                        $attributes['entityCollection'],
                        isset($attributes['repository']) ?
                            new Reference($attributes['repository']) :
                            null
                    ));
                }

                // doctrine bridge
                if ($container->hasDefinition('majora.doctrine.event_proxy')) {
                    $container
                        ->getDefinition('majora.doctrine.event_proxy')
                        ->addMethodCall('registerDoctrineLoader', array(
                            $attributes['entityClass'],
                            $loaderId
                        ))
                    ;
                }
            }
        }
    }
}
