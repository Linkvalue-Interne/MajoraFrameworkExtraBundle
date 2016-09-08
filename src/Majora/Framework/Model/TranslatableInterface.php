<?php

namespace Majora\Framework\Model;

/**
 * Interface for translatable models
 */
interface TranslatableInterface
{
    /**
     * define current object locale
     *
     * @param string $locale
     *
     * @return self
     */
    public function setLocale($locale);

    /**
     * define object default locale to use
     *
     * @param string $currentLocale
     * @return self
     */
    public function setDefaultLocale($defaultLocale);
}
