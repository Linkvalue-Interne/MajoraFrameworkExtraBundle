<?php

namespace Majora\Framework\Loader\Bridge\Doctrine;

use Majora\Framework\Loader\LoaderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Interface to implement on Doctrine loader interface
 */
interface DoctrineLoaderInterface extends LoaderInterface
{
    /**
     * Post entity loading triggered event
     */
    const POST_LOAD_EVENT = 'postLoad';

    /**
     * Return Closure which can custom every loaded objects
     *
     * Closure will be called with object at first arg :
     * @example
     *     return function(User $user) {
     *         $user->setLoadedAt('now');
     *     };
     *
     * @return Closure
     */
    public function getDelegate();
}
