<?php

namespace Majora\Bundle\FrameworkExtraBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Extension for "text" form type, used to provide a multiple languages
 * for widget, which renders an array containing a label for each supported locales.
 */
class TranslatableTextType extends AbstractType
{
    /**
     * @var array
     */
    protected $locales;

    /**
     * Construct.
     *
     * @param array $locales
     */
    public function __construct(array $locales = array())
    {
        $this->locales = $locales;
    }

    /**
     * @see FormInterface::getName()
     */
    public function getName()
    {
        return 'translatable_text';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('widget', TextType::class);
        $resolver->setAllowedValues('widget', array(
            TextType::class, TextareaType::class
        ));

        $resolver->setDefaults(array(
            'locales' => array(),
            'widget_options' => array(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $handledLocales = empty($options['locales']) ?
            $this->locales :
            array_intersect($this->locales, $options['locales'])
        ;
        if (empty($handledLocales)) {
            return;
        }

        $childrenOptions = array_replace_recursive(
            array('auto_initialize' => false),
            $options['widget_options']
        );

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($builder, $handledLocales, $childrenOptions, $options) {
            $form = $event->getForm();
            $data = $event->getData() ?: array();

            if (!is_array($data)) {
                throw new \InvalidArgumentException(sprintf('TranslatableText data have to be an array, %s given',
                    gettype($data)
                ));
            }

            // handle existing locales even if not supported
            $handledLocales = array_unique(array_merge(
                $handledLocales,
                array_keys($data)
            ));

            foreach ($handledLocales as $locale) {
                $form->add($builder
                    ->create($locale, $options['widget'], $childrenOptions)
                    ->getForm()
                );
            }
        });
    }
}
