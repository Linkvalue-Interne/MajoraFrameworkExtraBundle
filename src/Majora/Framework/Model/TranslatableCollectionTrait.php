<?php

namespace Majora\Framework\Model;

/**
 * Trait for translatable object collections
 *
 * @method forAll(Closure $p)
 */
trait TranslatableCollectionTrait
{
    /**
     * @see TranslatableInterface::setLocale()
     */
    public function setLocale($locale)
    {
        $this->forAll(function ($key, TranslatableInterface $element) use ($locale) {
            $element->setLocale($locale);

            return true;
        });

        return $this;
    }

    /**
     * @see TranslatableInterface::setDefaultLocale()
     */
    public function setDefaultLocale($defaultLocale)
    {
        $this->forAll(function ($key, TranslatableInterface $element) use ($defaultLocale) {
            $element->setDefaultLocale($defaultLocale);

            return true;
        });

        return $this;
    }
}
