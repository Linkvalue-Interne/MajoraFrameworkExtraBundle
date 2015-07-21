<?php

namespace Majora\Framework\Loader\Bridge\Form;

use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Trait which provides a bridge between majora loader and symfony forms
 *
 * @see DataTransformerInterface
 *
 * @property entityClass
 * @method retrieve($id) : CollectionableInterface
 */
trait DataTransformerLoaderTrait
{
    /**
     * Model -> View
     *
     * @see DataTransformerInterface::transform()
     */
    public function transform($entity)
    {
        if (null === $entity) {
            return '';
        }
        if (!is_subclass_of($entity, $this->entityClass)) {
            throw new \InvalidArgumentException(sprintf(
                'Unsupported entity "%s" into "%s" loader.',
                get_class($entity),
                __CLASS__
            ));
        }

        return $entity->getId();
    }

    /**
     * View -> Model
     *
     * @see DataTransformerInterface::reverseTransform()
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }
        if (!$entity = $this->retrieve($id)) {
            throw new TransformationFailedException(sprintf(
                '%s#%s cannot be found.',
                $this->entityClass,
                $id
            ));
        }

        return $entity;
    }
}
