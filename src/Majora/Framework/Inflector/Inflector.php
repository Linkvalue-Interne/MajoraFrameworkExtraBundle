<?php

namespace Majora\Framework\Inflector;

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
     *
     * @see for inspiration https://github.com/symfony/dependency-injection/blob/master/Container.php#L342
     */
    public function pascalize($string)
    {
        return ucfirst(strtr(
            ucwords(strtr($string, array('_' => ' ', '.' => '_ ', '\\' => '_ '))),
            array(' ' => '')
        ));
    }

    /**
     * format snake_case.
     *
     * @param string $string
     *
     * @return string
     *
     * @see for inspiration https://github.com/symfony/dependency-injection/blob/master/Container.php#L354
     */
    public function snakelize($string)
    {
        return strtolower(preg_replace(
            array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'),
            array('\\1_\\2', '\\1_\\2'),
            $string
        ));
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
        // special replacement map
        $slug = strtr($string, array(
            'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y'
        ));

        return trim(  // clear extra replacement chars
            preg_replace( // replace "non letter" and "digits"
                '/\W+/',
                $replacement,
                strtolower($slug)
            ),
            $replacement
        );
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
