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
