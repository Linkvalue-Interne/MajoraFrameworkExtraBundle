<?php

namespace Majora\Framework\Normalizer;

use Majora\Framework\Normalizer\Exception\InvalidScopeException;
use Majora\Framework\Normalizer\Exception\ScopeNotFoundException;
use Majora\Framework\Normalizer\Model\NormalizableInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Normalizer class implementing scoping compilation and object normalization construction.
 *
 * @see NormalizableInterface
 */
class MajoraNormalizer
{
    /**
     * @var MajoraNormalizer[]
     */
    private static $instancePool;

    /**
     * @var \ReflectionClass[]
     */
    private static $reflectionPool;

    /**
     * @var \Closure
     */
    private $readDelegate;

    /**
     * @var \Closure
     */
    private $writeDelegate;

    /**
     * @var PropertyAccessor
     */
    protected $propertyAccessor;

    /**
     * Create and return an instantiated normalizer, returns always the same throught this call.
     *
     * @param string $key optionnal normalizer key
     *
     * @return MajoraNormalizer
     */
    public static function createNormalizer($key = 'default')
    {
        return isset(self::$instancePool[$key]) ?
            self::$instancePool[$key] :
            self::$instancePool[$key] = new static(
            PropertyAccess::createPropertyAccessor()
        );
    }

    /**
     * Construct.
     *
     * @param PropertyAccessor $propertyAccessor
     */
    public function __construct(PropertyAccessor $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * Create and return a Closure available to read an object property through a property path or a private property.
     *
     * @return \Closure
     */
    private function createReadingDelegate()
    {
        return $this->readDelegate ?: $this->readDelegate = function ($property, PropertyAccessor $propertyAccessor) {
            switch (true) {

                // Public property / accessor case
                case $propertyAccessor->isReadable($this, $property) :
                    return $propertyAccessor->getValue($this, $property);

                // Private property / StdClass
                case property_exists($this, $property) || $this instanceof \StdClass:
                    return $this->$property;
            }

            throw new InvalidScopeException(sprintf(
                'Unable to read "%s" property from a "%s" object, any existing property path to read it in.',
                $property,
                get_class($this)
            ));
        };
    }

    /**
     * Normalize given normalizable object using given scope.
     *
     * @param NormalizableInterface $object
     * @param string                $scope
     *
     * @return array|string
     *
     * @throws ScopeNotFoundException If given scope not defined into given normalizable
     * @throws InvalidScopeException  If given scope requires an unaccessible field
     */
    public function normalize(NormalizableInterface $object, $scope = 'default')
    {
        $scopes = $object->getScopes();
        if (!isset($scopes[$scope])) {
            throw new ScopeNotFoundException(sprintf(
                'Invalid scope for %s object, only [%s] supported, "%s" given.',
                get_class($object),
                implode(', ', array_keys($scopes)),
                $scope
            ));
        }
        if (empty($scopes) || empty($scopes[$scope])) {
            return array();
        }

        $read = \Closure::bind(
            $this->createReadingDelegate(),
            $object,
            get_class($object)
        );

        if (is_string($scopes[$scope])) {
            return $read($scopes[$scope], $this->propertyAccessor);
        }

        $data = array();
        $stack = array($scopes[$scope]);
        do {
            $stackedField = array_shift($stack);
            foreach ($stackedField as $fieldConfig) {
                if (strpos($fieldConfig, '@') === false) {
                    $optionnal = false;
                    if (strpos($fieldConfig, '?') !== false) {
                        $fieldConfig = str_replace('?', '', $fieldConfig);
                        $optionnal = true;
                    }

                    // dont override previously setted value :
                    // first to inject always are field in asked scope, included one dont have to override
                    if (array_key_exists($fieldConfig, $data)) {
                        continue;
                    }

                    $value = $read($fieldConfig, $this->propertyAccessor);

                    // serializable child object ?
                    if ($value instanceof NormalizableInterface) {
                        $value = $value->normalize('default');
                    }

                    // date ?
                    if ($value instanceof \DateTime) {
                        $value = $value->format(\DateTime::ISO8601);
                    }

                    // nullable ?
                    if (!(is_null($value) && $optionnal)) {
                        $data[$fieldConfig] = $value;
                    }

                    continue;
                }

                list($field, $includeScope) = explode('@', $fieldConfig);

                if (empty($field)) { // internal scope
                    array_unshift($stack, $scopes[$includeScope]);
                    continue;
                }

                // external scopes : first in, last in
                if (isset($data[$field])) {
                    continue;
                }

                $relatedEntity = $read($field, $this->propertyAccessor);

                // serialize child entity
                if ($relatedEntity instanceof NormalizableInterface) {
                    $relatedEntity = $relatedEntity->normalize(
                        $includeScope ?: 'default'
                    );
                }

                $data[$field] = $relatedEntity;
            }
        } while (!empty($stack));

        return $data;
    }

    /**
     * Create and return a Closure available to write an object property through a property path or a private property.
     *
     * @return \Closure
     */
    private function createWrittingDelegate()
    {
        return $this->writeDelegate ?: $this->writeDelegate = function ($property, $value, PropertyAccessor $propertyAccessor) {
            switch (true) {

                // Public property / accessor case
                case $propertyAccessor->isWritable($this, $property) :
                    return $propertyAccessor->setValue($this, $property, $value);

                // Private property / StdClass
                case property_exists($this, $property) || $this instanceof \StdClass :
                    return $this->$property = $value;
            }

            throw new InvalidScopeException(sprintf(
                'Unable to set "%s" property into a "%s" object, any existing property path to write it in.',
                $property,
                get_class($this)
            ));
        };
    }

    /**
     * Denormalize given object data into given normalizable object or class
     * If class given, normalizer will try to inject data into constructor if class is not a NormalizableInterface.
     *
     * @param mixed         $data
     * @param object|string $normalizable normalizable object to denormalize in or an object class name
     *
     * @return NormalizableInterface
     */
    public function denormalize($data, $normalizable)
    {
        $class = is_string($normalizable) ?
            $normalizable : (
                $normalizable instanceof \ReflectionClass ?
                    $normalizable->name :
                    get_class($normalizable)
            )
        ;
        $reflection = isset(self::$reflectionPool[$class]) ?
            self::$reflectionPool[$class] :
            self::$reflectionPool[$class] = $normalizable instanceof \ReflectionClass ?
                $normalizable :
                new \ReflectionClass($class)
        ;

        $object = $normalizable;

        // Got reflection ? so build a new object
        if (is_string($object) || $object instanceof \ReflectionClass) {

            if (empty($data)) { // no data ? no worries !
                return $reflection->newInstance();
            }

            // Construct with parameters ? we will try to hydrate arguments from their names
            if ($reflection->hasMethod('__construct')
                && count($reflection->getMethod('__construct')->getParameters())
            ) {
                // @todo clever parameter ventilation

                return $reflection->newInstanceArgs((array) $data);
            }

            $object = $reflection->newInstance();
        }

        if (empty($data)) {
            return $object;
        }

        $write = \Closure::bind(
            $this->createWrittingDelegate(),
            $object,
            get_class($object)
        );

        foreach ($data as $property => $value) {

            // simple case : access property
            if (!$reflection->hasMethod($setter = sprintf('set%s', ucfirst($property)))) {
                $write($property, $value, $this->propertyAccessor);
                continue;
            }

            // extract setter class from type hinting
            $reflectionMethod = $reflection->getMethod($setter);
            $parameters = $reflectionMethod->getParameters();
            $setParameter = $parameters[0];

            // scalar or array ?
            if (!$setParameter->getClass() || $setParameter->isArray()) {
                $write($property, $value, $this->propertyAccessor);

                continue;
            }

            // nullable object ?
            if (empty($value)) {
                if ($setParameter->allowsNull()) {
                    $write($property, null, $this->propertyAccessor);
                }

                continue;
            }

            // callable ?
            if (is_callable($value)) {
                if ($setParameter->isCallable()) {
                    $write($property, $value, $this->propertyAccessor);
                }
            }

            $write(
                $property,
                $this->denormalize($value, $setParameter->getClass()),
                $this->propertyAccessor
            );
        }

        return $object;
    }
}
