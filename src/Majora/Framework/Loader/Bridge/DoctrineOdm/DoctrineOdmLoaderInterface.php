<?php

namespace Majora\Framework\Loader\Bridge\DoctrineOdm;

use Majora\Framework\Loader\LoaderInterface;

/**
 * Interface to implement on Doctrine loader interface.
 */
interface DoctrineOdmLoaderInterface extends LoaderInterface
{
    /**
     * Post entity loading triggered event.
     */
    const POST_LOAD_EVENT = 'postLoad';
}
