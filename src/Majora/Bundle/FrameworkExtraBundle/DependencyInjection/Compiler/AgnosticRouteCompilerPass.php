<?php

namespace Majora\Bundle\FrameworkExtraBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\ExpressionLanguage\Expression;

/**
 * Compiler pass for "majora.agnostic_route" tag handling
 */
class AgnosticRouteCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('majora.agnostic_url_generator')) {
            return;
        }

        $urlGenerator = $container->getDefinition('majora.agnostic_url_generator');
        $serviceTags = $container->findTaggedServiceIds('majora.agnostic_route');

        foreach ($serviceTags as $id => $tags) {
            foreach ($tags as $tag) {
                if (empty($tag['route'])) {
                    throw new \InvalidArgumentException(sprintf(
                        '"majora.agnostic_route" tag has to expose a "route" attribute.'
                    ));
                }
                $routeArguments = array();
                if (!empty($tag['arguments'])) {
                    if (!$container->hasParameter($tag['arguments'])) {
                        throw new \InvalidArgumentException(sprintf(
                            '"majora.agnostic_route" tag has to expose a valid container parameter key under "arguments" attribute.'
                        ));
                    }
                    $routeArguments = $container->getParameter($tag['arguments']);
                }

                $serviceDefinition = $container->getDefinition($id);
                $serviceDefinition->addMethodCall(
                    empty($tag['injector']) ? 'registerUrl' : $tag['injector'],
                    array(
                        empty($tag['alias']) ? $tag['route'] : $tag['alias'],
                        new Expression(sprintf(
                            'service(\'majora.agnostic_url_generator\').generate(\'%s\', {}, true)',
                            $tag['route']
                        )),
                        $routeArguments
                    )
                );
            }
        }
    }
}
