<?php

namespace Majora\Bundle\FrameworkExtraBundle\DependencyInjection\Compiler;

use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;

/**
 * Compiler pass used to guess extra validation
 * files from bundles.
 */
class ValidationCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('validator.builder')) {
            return;
        }

        $validatorBuilder = $container->getDefinition('validator.builder');
        $yamlMappings     = array();

        foreach ($container->getParameter('kernel.bundles') as $bundle) {
            $reflection = new \ReflectionClass($bundle);
            $directory = dirname($reflection->getFilename()).'/Resources/config/validation';
            if (!is_dir($directory)) {
                continue;
            }

            $finder = (new Finder())
                ->in($directory)
                ->name('*.yml')
            ;

            foreach ($finder as $fileInfo) {
                $yamlMappings[] = $fileInfo->getRealpath();
                $container->addResource(new FileResource(sprintf('%s/%s',
                    $directory,
                    $fileInfo->getFilename()
                )));
            }
        }

        if (count($yamlMappings) > 0) {
            $validatorBuilder->addMethodCall('addYamlMappings', array($yamlMappings));
        }
    }
}
