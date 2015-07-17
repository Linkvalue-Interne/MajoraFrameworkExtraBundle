<?php

namespace Majora\Framework\Loader\Bridge\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Trait which provides a bridge between majora loader and symfony security
 * user providers
 *
 * @property string entityClass
 * @property \ReflectionClass entityReflection
 */
trait UserProviderLoaderTrait
{
    /**
     * @see UserProviderInterface::loadUserByUsername()
     */
    public function loadUserByUsername($username)
    {
        $queryParameters = array('email' => $username);
        if ($this->entityReflection->implementsInterface('Majora\Framework\Model\EnablableInterface')) {
            $queryParameters['enabled'] = true;
        }

        if (!$person = $this->retrieveOne($queryParameters)) {
            throw new UsernameNotFoundException(sprintf(
                'User "%s" is not an active person.',
                $username
            ));
        }

        return $person;
    }

    /**
     * @see UserProviderInterface::refreshUser()
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf(
                'Instances of "%s" are not supported.',
                get_class($user)
            ));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @see UserProviderInterface::supportsClass()
     */
    public function supportsClass($class)
    {
        return $class == $this->entityClass;
    }
}
