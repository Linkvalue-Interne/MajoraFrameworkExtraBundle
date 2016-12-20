<?php

namespace Majora\Bundle\FrameworkExtraBundle;

use Majora\Bundle\FrameworkExtraBundle\DependencyInjection\Compiler\ActionRegisterCompilerPass;
use Majora\Bundle\FrameworkExtraBundle\DependencyInjection\Compiler\AliasRegisterCompilerPass;
use Majora\Bundle\FrameworkExtraBundle\DependencyInjection\Compiler\InMemoryDataLoadCompilerPass;
use Majora\Bundle\FrameworkExtraBundle\DependencyInjection\Compiler\LoaderBridgeFormCompilerPass;
use Majora\Bundle\FrameworkExtraBundle\DependencyInjection\Compiler\LoaderCompilerPass;
use Majora\Bundle\FrameworkExtraBundle\DependencyInjection\Compiler\SerializerCompilerPass;
use Majora\Bundle\FrameworkExtraBundle\DependencyInjection\Compiler\ValidationCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\HttpKernel\KernelInterface;

class MajoraFrameworkExtraBundle extends Bundle
{
    /**
     * @var FileLocator
     */
    protected $fileLocator;

    /**
     * Construct.
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->fileLocator = new FileLocator($kernel);
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new SerializerCompilerPass());
        $container->addCompilerPass(new ValidationCompilerPass());
        $container->addCompilerPass(new LoaderCompilerPass());
        $container->addCompilerPass(new InMemoryDataLoadCompilerPass($this->fileLocator));
        $container->addCompilerPass(new ActionRegisterCompilerPass());
        $container->addCompilerPass(new AliasRegisterCompilerPass());
        $container->addCompilerPass(new LoaderBridgeFormCompilerPass());
    }
}
