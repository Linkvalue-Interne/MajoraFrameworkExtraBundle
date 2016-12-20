<?php

namespace Majora\Framework\Loader\Tests\Bridge\Form\Type;

use Prophecy\Argument;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Majora\Framework\Model\EntityCollection;
use Majora\Framework\Loader\Bridge\Form\Type\EntityCollectionType;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntityCollectionTypeTest extends TypeTestCase
{
    /**
     * Test options resolver calls during options configuration.
     */
    public function testConfigureOptions()
    {
        $resolver = $this->prophesize(OptionsResolver::class);

        $resolver
            ->setRequired(
                [
                    'loader_alias',
                    'loader_method',
                ]
            )
            ->shouldBeCalled()
            ->willReturn($resolver);

        $resolver
            ->setAllowedTypes('loader_alias', 'string')
            ->shouldBeCalled()
            ->willReturn($resolver);

        $resolver
            ->setAllowedTypes('loader_method', ['null', 'string'])
            ->shouldBeCalled()
            ->willReturn($resolver);

        $resolver
            ->setDefaults(Argument::any())
            ->shouldBeCalled()
            ->willReturn($resolver);

        $resolver
            ->setAllowedValues('loader_alias', Argument::any())
            ->shouldBeCalled()
            ->willReturn($resolver);

        $form = new EntityCollectionType();
        $form->configureOptions($resolver->reveal());
    }

    /**
     * Test configure options when the loader is missing.
     */
    public function testConfigureOptionsExceptionLoaderNotFound()
    {
        $resolver = new OptionsResolver();

        $type = new EntityCollectionType();
        $type->configureOptions($resolver);

        $this->expectException(InvalidOptionsException::class);

        $resolver->resolve(
            [
                'loader_alias' => 'test1',
            ]
        );
    }

    /**
     * Test configure options when a loader method that does not exists.
     */
    public function testConfigureOptionsExceptionLoaderMethodNotFound()
    {
        $loader = $this->prophesize(TestLoader::class)->reveal();
        $resolver = new OptionsResolver();

        $type = new EntityCollectionType();
        $type->registerLoader('test', $loader);
        $type->configureOptions($resolver);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(sprintf('Method "bad_method" not found in "%s".', get_class($loader)));

        $resolver->resolve(
            [
                'loader_alias' => 'test',
                'loader_method' => 'bad_method',
            ]
        );
    }

    /**
     * Test configure options when data returned by the loader is not an EntityCollection.
     */
    public function testConfigureOptionsExceptionWrongData()
    {
        $resolver = new OptionsResolver();
        $loaderProphecy = $this->prophesize(TestLoader::class);

        $loaderProphecy
            ->choiceList()
            ->willReturn(null);

        $loader = $loaderProphecy->reveal();

        $type = new EntityCollectionType();
        $type->registerLoader('test', $loader);
        $type->configureOptions($resolver);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Registered loading method %s::%s() should return an "%s", "%s" returned.',
                get_class($loader),
                'choiceList',
                EntityCollection::class,
                gettype(null)
            )
        );

        $resolver->resolve(
            [
                'loader_alias' => 'test',
            ]
        );
    }

    /**
     * Test data when submit form.
     */
    public function testSubmitValidData()
    {
        $object = new TestObject(['id' => 1, 'name' => 'john']);
        $formData = [
            'test' => $object->id,
        ];

        $form = $this->factory->create(TestFormType::class);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($object, $form->get('test')->getData());

        $view = $form->createView();
        $children = $view->children;

        $this->assertArrayHasKey('test', $children);
    }

    /**
     * Test parent getter.
     */
    public function testGetParent()
    {
        $type = new EntityCollectionType();

        $this->assertEquals($type->getParent(), ChoiceType::class);
    }

    /**
     * Test loader registration.
     */
    public function testRegisterLoader()
    {
        $loader = $this->prophesize(TestLoader::class)->reveal();

        $type = new EntityCollectionType();
        $type->registerLoader('test', $loader);

        $reflection = new \ReflectionProperty(EntityCollectionType::class, 'loaders');
        $reflection->setAccessible(true);
        $loaders = $reflection->getValue($type);

        reset($loaders);

        $this->assertEquals('test', key($loaders));
        $this->assertEquals($loader, current($loaders));
    }

    /**
     * Test loader registration with an alias that already exists.
     */
    public function testRegisterLoaderAliasAlreadyExists()
    {
        $loader1 = $this->prophesize(TestLoader::class)->reveal();
        $loader2 = $this->prophesize(TestLoader::class)->reveal();

        $this->expectException(\RuntimeException::class);

        $type = new EntityCollectionType();
        $type->registerLoader('test', $loader1);
        $type->registerLoader('test', $loader2);
    }

    /**
     * Test loader exists method.
     */
    public function testHasLoader()
    {
        $loader = $this->prophesize(TestLoader::class)->reveal();

        $type = new EntityCollectionType();
        $type->registerLoader('test', $loader);

        $method = new \ReflectionMethod(EntityCollectionType::class, 'hasLoader');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($type, ['test']));
        $this->assertFalse($method->invokeArgs($type, ['toto']));
    }

    /**
     * Test loader getter.
     */
    public function testGetLoader()
    {
        $loader = $this->prophesize(TestLoader::class)->reveal();;

        $type = new EntityCollectionType();
        $type->registerLoader('test', $loader);

        $method = new \ReflectionMethod(EntityCollectionType::class, 'getLoader');
        $method->setAccessible(true);

        $this->assertEquals($loader, $method->invokeArgs($type, ['test']));
    }

    /**
     * Test loader getter exception.
     */
    public function testGetLoaderException()
    {
        $loader = $this->prophesize(TestLoader::class)->reveal();

        $type = new EntityCollectionType();
        $type->registerLoader('test', $loader);

        $this->expectException(\RuntimeException::class);

        $method = new \ReflectionMethod(EntityCollectionType::class, 'getLoader');
        $method->setAccessible(true);
        $method->invokeArgs($type, ['toto']);
    }

    /**
     * Extensions for our form.
     *
     * @return array
     */
    protected function getExtensions()
    {
        $type = new EntityCollectionType();
        $type->registerLoader('test', new TestLoader());

        return [
            new PreloadedExtension([$type], []),
        ];
    }
}

/**
 * Loader for tests.
 */
class TestLoader
{
    public function choiceList()
    {
        return new EntityCollection(
            [
                new TestObject(['id' => 1, 'name' => 'john']),
                new TestObject(['id' => 2, 'name' => 'doe']),
            ]
        );
    }
}

/**
 * Class TestObject for tests.
 */
class TestObject
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * TestObject constructor.
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->name;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->$name;
    }
}

/**
 * Class TestFormType for tests.
 */
class TestFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'test',
                EntityCollectionType::class,
                [
                    'loader_alias' => 'test',
                ]
            );
    }
}