<?php

namespace Majora\Bundle\FrameworkExtraBundle\DependencyInjection\Compiler;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Yaml;

/**
 * Compiler pass which guess and load all memory data stores.
 */
class InMemoryDataLoadCompilerPass implements CompilerPassInterface
{
    /**
     * @var FileLocatorInterface
     */
    protected $fileLocator;

    /**
     * @var OptionsResolver
     */
    protected $optionsResolver;

    /**
     * Construct.
     *
     * @param FileLocator $fileLocator
     */
    public function __construct(FileLocatorInterface $fileLocator)
    {
        $this->fileLocator = $fileLocator;
        $this->optionsResolver = new OptionsResolver();
        $this->optionsResolver->setDefaults(array(
            'callback' => 'registerData',
        ));
        $this->optionsResolver->setDefined('file');
        $this->optionsResolver->setDefined('parameter');
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $inMemoryFilesTags = $container->findTaggedServiceIds('majora.loader.in_memory');

        foreach ($inMemoryFilesTags as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                $options = $this->optionsResolver->resolve($attributes);
                $loaderDefinition = $container->getDefinition($serviceId);

                // File loading
                if (!empty($options['file'])) {
                    $file = new SplFileInfo(
                        $this->fileLocator->locate($options['file']),
                        '',
                        ''
                    );
                    $loaderDefinition->addMethodCall($options['callback'], array(
                        Yaml::parse($file->getContents()),
                    ));

                    $container->addResource(new FileResource($file->getRealPath()));
                }

                // Parameter loading
                if (!empty($options['parameter'])) {
                    $loaderDefinition->addMethodCall($options['callback'], array(
                        $container->getParameter($options['parameter']),
                    ));
                    $container->getParameterBag()->remove($options['parameter']);
                }
            }
        }
    }
}
