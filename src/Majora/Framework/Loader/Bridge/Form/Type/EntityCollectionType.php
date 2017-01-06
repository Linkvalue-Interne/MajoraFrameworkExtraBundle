<?php

namespace Majora\Framework\Loader\Bridge\Form\Type;

use Majora\Framework\Model\EntityCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class EntityCollectionType.
 */
class EntityCollectionType extends AbstractType
{
    /**
     * @var array
     */
    private $loaders = [];

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired(
                [
                    'loader_alias',
                    'loader_method',
                ]
            )
            ->setAllowedTypes('loader_alias', 'string')
            ->setAllowedTypes('loader_method', ['null', 'string'])
            ->setDefaults(
                [
                    'loader_method' => 'choiceList',
                    'choice_value' => 'id',
                    'choice_label' => function ($choice) {
                        return (string)$choice;
                    },
                    'choices' => function (Options $options) {

                        $loader = $this->getLoader($options['loader_alias']);

                        if (method_exists($loader, $options['loader_method']) === false) {
                            throw new \RuntimeException(
                                sprintf('Method "%s" not found in "%s".', $options['loader_method'], get_class($loader))
                            );
                        };

                        $data = $loader->{$options['loader_method']}();

                        if (!($data instanceof EntityCollection)) {
                            throw new \RuntimeException(
                                sprintf(
                                    'Registered loading method %s::%s() should return an "%s", "%s" returned.',
                                    get_class($loader),
                                    $options['loader_method'],
                                    EntityCollection::class,
                                    gettype($data)
                                )
                            );
                        }

                        return $data;
                    },
                ]
            )
            ->setAllowedValues(
                'loader_alias',
                function ($value) {
                    return $this->hasLoader($value);
                }
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * Register a loader.
     *
     * @param $alias
     * @param $loader
     */
    public function registerLoader($alias, $loader)
    {
        if ($this->hasLoader($alias)) {
            throw new \RuntimeException(sprintf('Alias "%s" already used by another form loader', $alias));
        }

        $this->loaders[$alias] = $loader;
    }

    /**
     * Test if a loader exists by his alias.
     *
     * @param $alias
     *
     * @return bool
     */
    private function hasLoader($alias)
    {
        return isset($this->loaders[$alias]);
    }

    /**
     * Get a loader by his alias.
     *
     * @param $alias
     *
     * @return mixed
     */
    private function getLoader($alias)
    {
        if (!$this->hasLoader($alias)) {
            throw new \RuntimeException(sprintf('Form loader with alias "%s" not found.', $alias));
        }

        return $this->loaders[$alias];
    }
}
