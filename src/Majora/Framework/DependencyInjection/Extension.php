<?php

namespace Majora\Framework\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension as SymfonyExtension;

/**
 * Extension class used for custom majora related bundle
 * container compilation.
 */
abstract class Extension extends SymfonyExtension
{
    /**
     * register base majora service aliases.
     *
     * @param ContainerBuilder $container
     * @param string           $entity
     * @param array            $config
     */
    protected function registerAliases(ContainerBuilder $container, $entity, array $config)
    {
        $container->setAlias(
            sprintf('%s.repository', $entity),
            sprintf('%s.%s_repository', $entity, $config['persistence'])
        );
        $container->setAlias(
            sprintf('%s.domain', $entity),
            sprintf('%s.%s_domain', $entity, $config['domain'])
        );
        $container->setAlias(
            sprintf('%s.loader', $entity),
            sprintf('%s.%s_loader', $entity, $config['loader'])
        );
    }
}
