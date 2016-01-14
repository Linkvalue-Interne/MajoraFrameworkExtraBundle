<?php

namespace Majora\Framework\Form\Extension\Json\Type;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\RequestHandlerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler;
use Majora\Framework\Form\Extension\Json\JsonRequestHandler;
use Majora\Framework\Form\Extension\Json\JsonExtensionListener;

class FormTypeJsonExtension extends AbstractTypeExtension
{
    /**
     * @var RequestHandlerInterface
     */
    private $requestHandler;

    /**
     * @param RequestHandlerInterface $requestHandler
     */
    public function __construct(RequestHandlerInterface $requestHandler = null)
    {
        $this->requestHandler = $requestHandler ?: new JsonRequestHandler(new HttpFoundationRequestHandler());
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setRequestHandler($this->requestHandler);
        if ($options['json_format']) {
            $builder->addEventSubscriber(new JsonExtensionListener());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('json_format', false);
        $resolver->setAllowedTypes('json_format', 'boolean');
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return FormType::class;
    }
}
