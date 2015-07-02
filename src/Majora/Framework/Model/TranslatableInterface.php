<?php

namespace Majora\Framework\Model;

/**
 * Interface for translatable models
 */
interface TranslatableInterface
{
    /**
     * define currentLocale
     *
     * @param string $currentLocale
     *
     * @return self
     */
    public function setCurrentLocale($currentLocale);

    /**
     * define defaultLocale
     *
     * @param string $currentLocale
     * @return self
     */
    public function setDefaultLocale($defaultLocale);
}
