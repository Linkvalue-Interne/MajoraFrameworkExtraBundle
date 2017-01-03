<?php

namespace Majora\Framework\Tests\Routing;

use Majora\Framework\Routing\AgnosticUrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

/**
 * Class AgnosticUrlGeneratorTest.
 *
 * @see \Majora\Framework\Routing\AgnosticUrlGenerator
 */
class AgnosticUrlGeneratorTest extends \PHPUnit_Framework_TestCase
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
     * @var AgnosticUrlGenerator
     */
    protected $agnosticUrlGenerator;

    /**
     * Sets up.
     */
    protected function setUp()
    {
        $this->context = $this->createMock(RequestContext::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        $this->urlGenerator
            ->expects($this->any())
            ->method('getContext')
            ->willReturn($this->context);

        $this->agnosticUrlGenerator = new AgnosticUrlGenerator($this->urlGenerator);
    }

    /**
     * Tears down.
     */
    protected function tearDown()
    {
        unset(
            $this->context,
            $this->urlGenerator,
            $this->agnosticUrlGenerator
        );
    }

    /**
     * Test AgnosticUrlGenerator::__constructor() method.
     */
    public function testConstructor()
    {
        $urlGeneratorReflection = new \ReflectionProperty(AgnosticUrlGenerator::class, 'urlGenerator');
        $urlGeneratorReflection->setAccessible(true);

        $contextReflection = new \ReflectionProperty(AgnosticUrlGenerator::class, 'context');
        $contextReflection->setAccessible(true);

        $this->assertEquals($this->urlGenerator, $urlGeneratorReflection->getValue($this->agnosticUrlGenerator));
        $this->assertEquals($this->context, $contextReflection->getValue($this->agnosticUrlGenerator));
    }

    /**
     * Test AgnosticUrlGenerator::setContext() method.
     */
    public function testContextSetter()
    {
        $context = $this->createMock(RequestContext::class);

        $context
            ->expects($this->once())
            ->method('setBaseUrl')
            ->with('http://localhost');

        $context
            ->expects($this->once())
            ->method('getBaseUrl')
            ->willReturn('http://localhost/app_dev.php');

        $this->agnosticUrlGenerator->setContext($context);
    }

    /**
     * Test AgnosticUrlGenerator::getContext() method.
     */
    public function testContextGetter()
    {
        $this->assertEquals($this->context, $this->agnosticUrlGenerator->getContext());
    }

    /**
     * Test AgnosticUrlGenerator::generate() method.
     */
    public function testGenerate()
    {
        $name = 'my_route';
        $parameters = ['id' => 1];

        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with($name, $parameters, UrlGeneratorInterface::ABSOLUTE_PATH);

        $this->agnosticUrlGenerator->generate($name, $parameters);
    }
}
