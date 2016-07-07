<?php

namespace Majora\Bundle\FrameworkExtraBundle\DependencyInjection\Compiler;

use Majora\Framework\Loader\Bridge\Doctrine\AbstractDoctrineLoader;
use Majora\Framework\Loader\LoaderInterface;
use Majora\Framework\Model\CollectionableInterface;
use Majora\Framework\Model\LazyPropertiesInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to register loaders setUp.
 */
class LoaderCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     *
     * Processes "majora.loader" tags
     */
    public function process(ContainerBuilder $container)
    {
        $loaderTags = $container->findTaggedServiceIds('majora.loader');

        foreach ($loaderTags as $loaderId => $tags) {
            $loaderDefinition = $container->getDefinition($loaderId);
            $loaderReflection = new \ReflectionClass($loaderDefinition->getClass());

            foreach ($tags as $attributes) {
                $method = $loaderReflection->implementsInterface(LoaderInterface::class) ?
                    'configureMetadata' : ($loaderReflection->hasMethod('setUp') ?
                        'setUp' : ''
                    )
                ;
                if (isset($attributes['entityClass']) || isset($attributes['entityCollection'])) {
                    @trigger_error('"entityClass" and "entityCollection" attributes for tag "majora.loader" are deprecated and will be removed in 2.0. Please "entity" and "collection" instead.', E_USER_DEPRECATED);
                }
                $entityClass = isset($attributes['entity']) ? $attributes['entity'] : $attributes['entityClass'];
                $collectionClass = isset($attributes['collection']) ? $attributes['collection'] : $attributes['entityCollection'];
                $entityReflection = new \ReflectionClass($entityClass);

                // configureMetadata() call configuration
                if ($method) {
                    if (!$entityReflection->implementsInterface(CollectionableInterface::class)) {
                        throw new \InvalidArgumentException(sprintf(
                            'Cannot support "%s" class into "%s" : managed items have to be %s.',
                            $entityClass,
                            $loaderDefinition->getClass(),
                            CollectionableInterface::class
                        ));
                    }
                    $arguments = array(
                        $entityClass,
                        array_map(
                            function ($property) { return $property->getName(); },
                            $entityReflection->getProperties()
                        ),
                        $collectionClass,
                    );

                    $loaderDefinition->addMethodCall($method, $arguments);
                }

                // Doctrine case
                if ($loaderReflection->isSubclassOf(AbstractDoctrineLoader::class)) {

                    // "repository" attribute key only supported for doctrine loaders
                    // Repository is injected through mutator to avoid circular references
                    // with Doctrine events and connection
                    if (isset($attributes['repository'])) {
                        $loaderDefinition->addMethodCall(
                            'setEntityRepository',
                            array(new Reference($attributes['repository']))
                        );
                    }

                    // for Doctrine, loaders cannot self enable objects lazy loading
                    // due to general event trigger into all listener for each entites
                    // so we have to check class / attribute and register service into event proxy
                    if ($container->hasDefinition('majora.doctrine.event_proxy') && !empty($attributes['lazy'])) {
                        if (!$entityReflection->implementsInterface(LazyPropertiesInterface::class)) {
                            throw new \InvalidArgumentException(sprintf(
                                'Class %s has to implement %s to be able to lazy load her properties.',
                                $entityClass,
                                LazyPropertiesInterface::class
                            ));
                        }
                        $container->getDefinition('majora.doctrine.event_proxy')
                            ->addMethodCall('registerDoctrineLazyLoader', array(
                                $entityClass,
                                new Reference($loaderId),
                            ))
                        ;
                    }
                }
            }
        }
    }
}
