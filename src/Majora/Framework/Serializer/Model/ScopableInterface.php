<?php

namespace Majora\Framework\Serializer\Model;

@trigger_error('The '.__NAMESPACE__.'\ScopableInterface class is deprecated and will be removed in 2.0. Use Majora\Framework\Normalizer\Model\NormalizableInterface instead.', E_USER_DEPRECATED);

/**
 * Interface to implements on all scopable models.
 *
 * @deprecated
 */
interface ScopableInterface
{
    /**
     * Returns an indexed list of views of model as a list of fields or accessible methods.
     *
     * @example
     *    return array(
     *        'default'        => array('id', 'code', 'label'),
     *        'plain_field'    => 'id',
     *        'related_scope'  => array('@default', 'related_entity@related_scope', 'created_at', 'updated_at'),
     *        'optionnal'      => array('@related_scope', '?optionnal')
     *    );
     *
     * @return array
     */
    public static function getScopes();
}
