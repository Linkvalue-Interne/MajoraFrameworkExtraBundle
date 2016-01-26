<?php

namespace Majora\Framework\Routing;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

/**
 * Base routing proxy which strips debug base urls in order
 * to handle externals sources even with custom dev front controllers
 */
class AgnosticUrlGenerator implements UrlGeneratorInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var RequestContext
     */
    protected $context;

    /**
     * Construct
     *
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
        $this->setContext($urlGenerator->getContext());
    }

    /**
     * @see RequestContextAwareInterface::setContext()
     */
    public function setContext(RequestContext $context)
    {
        $this->context = clone $context;
        $this->context->setBaseUrl(preg_replace(
            '/(\\/[\w]+_dev.php)/',
            '',
            $context->getBaseUrl()
        ));
    }

    /**
     * @see RequestContextAwareInterface::getContext()
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @see UrlGeneratorInterface::generate()
     */
    public function generate($name, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $proxiedContext = $this->urlGenerator->getContext();
        $this->setContext($proxiedContext);

        $this->urlGenerator->setContext($this->getContext());
        $url = $this->urlGenerator->generate($name, $parameters, $referenceType);

        $this->urlGenerator->setContext($proxiedContext);

        return $url;
    }
}
