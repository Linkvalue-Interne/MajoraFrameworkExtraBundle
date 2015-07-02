<?php

namespace Majora\Framework\Model;

/**
 * Trait for translatable objects
 */
trait TranslatableTrait
{
    protected $currentLocale;
    protected $defaultLocale;

    /**
     * @see TranslatableInterface::setCurrentLocale()
     */
    public function setCurrentLocale($currentLocale)
    {
        $this->currentLocale = $currentLocale;

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
        if ($this->currentLocale && isset($translationData[$this->currentLocale])) {
            return $translationData[$this->currentLocale];
        }

        // default locale matched
        return $this->defaultLocale && isset($translationData[$this->defaultLocale]) ?
            $translationData[$this->defaultLocale] :
            $default
        ;
    }
}
