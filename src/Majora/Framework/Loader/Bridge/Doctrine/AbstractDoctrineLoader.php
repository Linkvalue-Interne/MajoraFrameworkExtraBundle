<?php

namespace Majora\Framework\Loader\Bridge\Doctrine;

use Doctrine\ORM\EntityRepository;
use Majora\Framework\Loader\LoaderInterface;

/**
 * Abstract class for doctrine loaders.
 */
abstract class AbstractDoctrineLoader implements LoaderInterface
{
    /**
     * @var EntityRepository
     */
    private $entityRepository;

    /**
     * Define Doctrine loader related doctrine repository for current entity.
     *
     * @param EntityRepository $entityRepository
     */
    public function setEntityRepository(EntityRepository $entityRepository)
    {
        $this->entityRepository = $entityRepository;

        return $this;
    }

    /**
     * Return entity repository.
     *
     * @return EntityRepository
     */
    protected function getEntityRepository()
    {
        if (!$this->entityRepository) {
            throw new \RuntimeException(sprintf(
                'Any defined entity repository into "%s" loader, consider calling "setEntityRepository()" right after object creation.',
                get_class($this)
            ));
        }

        return $this->entityRepository;
    }
}
