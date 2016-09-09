<?php

namespace Majora\Framework\Model;

/**
 * Trait for translatable objects
 */
trait TranslatableTrait
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $defaultLocale;

    /**
     * @see TranslatableInterface::setLocale()
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @see TranslatableInterface::setDefaultLocale()
     */
    public function setDefaultLocale($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;

        return $this;
    }

    /**
     * return translation matching current locale from given translation data
     *
     * @param array $translationData
     * @param string $default
     *
     * @return string
     */
    protected function getTranslation(array $translationData, $default = '')
    {
        // undefined locale
        if (empty($translationData)) {
            return $default;
        }

        // current locale matched
        if ($this->locale && isset($translationData[$this->locale])) {
            return $translationData[$this->locale];
        }

        // default locale matched
        return $this->defaultLocale && isset($translationData[$this->defaultLocale]) ?
            $translationData[$this->defaultLocale] :
            $default
        ;
    }
}
