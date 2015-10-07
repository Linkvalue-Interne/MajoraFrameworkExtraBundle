<?php

namespace Majora\Framework\Inflector;

use Symfony\Component\DependencyInjection\Container;

/**
 * Inflector class which build replacements format
 * from given vars.
 */
class Inflector
{
    protected $replacements;

    /**
     * construct.
     *
     * @param array $patterns
     */
    public function __construct(array $patterns = array())
    {
        $this->replacements = array();
        foreach ($patterns as $pattern => $replacement) {
            $this->replacements[$this->camelize($pattern)]  = $this->camelize($replacement);
            $this->replacements[$this->pascalize($pattern)] = $this->pascalize($replacement);
            $this->replacements[$this->snakelize($pattern)] = $this->snakelize($replacement);
            $this->replacements[$this->spinalize($pattern)] = $this->spinalize($replacement);
            $this->replacements[$this->uppercase($pattern)] = $this->uppercase($replacement);
        }
    }

    /**
     * return all replacements.
     *
     * @return array
     */
    public function all()
    {
        return $this->replacements;
    }

    /**
     * translate given source string with setted replacement patterns.
     *
     * @param string $source
     *
     * @return string
     */
    public function translate($source)
    {
        return strtr($source, $this->replacements);
    }

    /**
     * format camelCase.
     *
     * @param string $string
     *
     * @return string
     */
    public function camelize($string)
    {
        return lcfirst($this->pascalize($string));
    }

    /**
     * format PascalCase.
     *
     * @param string $string
     *
     * @return string
     */
    public function pascalize($string)
    {
        return ucfirst(Container::camelize($string));
    }

    /**
     * format snake_case.
     *
     * @param string $string
     *
     * @return string
     */
    public function snakelize($string)
    {
        return strtolower(Container::underscore($string));
    }

    /**
     * format spinal-case.
     *
     * @param string $string
     *
     * @return string
     */
    public function spinalize($string)
    {
        return str_replace('_', '-', $this->snakelize($string));
    }

    /**
     * format UPPER_CASE.
     *
     * @param string $string
     *
     * @return string
     */
    public function uppercase($string)
    {
        return strtoupper($this->snakelize($string));
    }

    /**
     * Replace every "non-word" "non-ascii" characters in $string by $replacement.
     *
     * @param string $string
     * @param string $replacement Default value is '-'.
     *
     * @return string
     */
    public function slugify($string, $replacement = '-')
    {
        $slug = $string;

        // @todo handle transliteration
//        if (function_exists('iconv')) {
//            $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
//        }

        // replace "non letter" and "digits"
        $slug = preg_replace('/\W+/', $replacement, $slug);

        // trim
        $slug = trim($slug, $replacement);

        // if slug is empty, returns replacement
        if (empty($slug)) {
            return $replacement;
        }

        return $slug;
    }

    /**
     * Normalize given data
     * If an array is given, normalize keys, according to given method
     *
     * @param string|array &$data
     * @param string       $format
     *
     * @return string|array
     *
     * @see for inspiration https://github.com/FriendsOfSymfony/FOSRestBundle/blob/master/Normalizer/CamelKeysNormalizer.php
     */
    public function normalize(&$data, $format)
    {
        if (!is_array($data)) {
            return $this->$format($data);
        }
        foreach ($data as $key => $value) {

            // already formatted ?
            if ($key != ($normalizedKey = $this->$format($key))) {
                if (array_key_exists($normalizedKey, $data)) {
                    throw new \InvalidArgumentException(sprintf(
                        'Both "%s" and %s("%s") keys exists, abord normalizing.',
                        $key, $format, $normalizedKey
                    ));
                }
                unset($data[$key]);
                $data[$normalizedKey] = $value;
                $key = $normalizedKey;
            }

            // iterate over child keys
            if (is_array($value)) {
                $this->normalize($data[$key], $format);
            }
        }

        return $data;
    }

    /**
     * Replace '/' and '\' by current OS directory separator.
     *
     * This won't return the full path from the system root to a file, use realpath() or SplFileInfo::getRealPath() instead.
     *
     * @param string $string
     *
     * @return string
     *
     * @see http://php.net/php_uname
     */
    public function directorize($string)
    {
        return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $string);
    }

    /**
     * Force path to be UNIX style (i.e. '/path/to/something').
     *
     * @param string $path
     *
     * @return string
     */
    public function unixizePath($path)
    {
        // Handle system root (e.g. 'c:\home' => '/home')
        $path = preg_replace('/^[a-z]:\\\/i', '/', $path);
        // Handle directory separators
        return str_replace('\\', '/', $path);
    }
}
