<?php

namespace Majora\Bundle\FrameworkExtraBundle\DependencyInjection\Compiler;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to register actions into related factories
 */
class ActionRegisterCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // retrieve factories
        $factoriesMap = new ArrayCollection();
        $factoriesTags = $container->findTaggedServiceIds('majora.domain.action_factory');
        foreach ($factoriesTags as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                if (empty($attributes['namespace'])) {
                    throw new \InvalidArgumentException(sprintf('
                        "majora.domain.action_factory" tags into "%s" service has to provide "namespace" attribute.',
                        $serviceId
                    ));
                }
                $factoriesMap->set(            // only one factory in each namespaces
                    $attributes['namespace'],
                    $container->getDefinition($serviceId)
                );
            }
        }

        // retrieve actions
        $actionsTags = $container->findTaggedServiceIds('majora.domain.action');
        foreach ($actionsTags as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                if (empty($attributes['alias']) || empty($attributes['namespace'])) {
                    throw new \InvalidArgumentException(sprintf('
                        "majora.domain.action" tags into "%s" service has to provide "alias" and "namespace" attributes.',
                        $serviceId
                    ));
                }
                if (!$factoriesMap->containsKey($attributes['namespace'])) {
                    throw new \InvalidArgumentException(sprintf('
                        Any action factory defined for "%s" namespace, defined into "%s" service.',
                        $attributes['namespace'],
                        $serviceId
                    ));
                }

                $factoriesMap->get($attributes['namespace'])
                    ->addMethodCall('registerAction', array(
                        $attributes['alias'],
                        new Reference($serviceId)
                    ))
                ;
            }
        }
    }
}
