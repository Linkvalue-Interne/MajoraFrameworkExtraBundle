<?php

namespace Majora\Bundle\FrameworkExtraBundle\DependencyInjection\Compiler;

use InvalidArgumentException;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Yaml\Yaml;

/**
 * Compiler pass to guess all fixtures loader, and load data on cache.
 */
class FixturesCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $serializerDef      = $container->getAlias('serializer');
        $fixturesLoadersDef = $container->findTaggedServiceIds('majora.fixtures_repository');
        $bundles            = $container->getParameter('kernel.bundles');

        foreach ($fixturesLoadersDef as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (empty($attribute['collection']) || empty($attribute['source-file'])) {
                    throw new InvalidArgumentException(sprintf(
                        'You has to provide a Majora\Framework\Model\EntityCollection class name under "collection" tag attribute and an existing yaml file path under "source-file" tag attribute'
                    ));
                }

                $sourcePath = $attribute['source-file'];

                // looks for a bundle ref path like '@xxxxBundle/'
                if (!is_file($sourcePath)
                    && preg_match('/^\@([\w]+Bundle)/', $sourcePath, $matches)
                    && !empty($bundles[$matches[1]])
                ) {
                    $reflection = new \ReflectionClass($bundles[$matches[1]]);
                    $directory  = dirname($reflection->getFilename());
                    if (is_dir($directory)) {
                        $sourcePath = str_replace('@'.$matches[1], realpath($directory), $sourcePath);
                    }
                }

                if (!is_file($sourcePath)) {
                    throw new InvalidArgumentException(sprintf(
                        'Provided source file path isnt a file path, or a bundle related path, "%s" given',
                        $attribute['source-file']
                    ));
                }

                $container->addResource(new FileResource($sourcePath));

                $container->getDefinition($id)
                    ->addMethodCall('setUp', array(
                        Yaml::parse(file_get_contents($sourcePath)),
                        $attribute['collection'],
                        new Reference($serializerDef->__toString()),
                    ))
                ;
            }
        }
    }
}
