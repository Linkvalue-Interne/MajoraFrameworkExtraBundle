# Normalizer and serializer component

These components are another approach for normalization (transform an object from/to standard classes or array) and serialization (object to / from strings).

We wanted to get a simple serializer, without many librairy magical integrations like Doctrine, so be it !

This component is based on a simple assertion : an object normalization should be designed into the object itself, programmatically, not with a pseudo language into comments, or within a fat configuration file. Just like `__toString()` method.

Majora Normalizer use a light schema system to design normalization views, called "scopes". The same as Symfony Normalizer "groups", but defineds into object, into a proper function. The improvement come from the scope chaining feature :the scope is localized into the normalized entity, and is not propagated to entity dependencies: you can define another one into sub objects, for each of them.
The goal is to reduce object output weight, with less circular references, and more field exposition control.

## Installation

Install and enable MajoraFrameworkExtraBundle (see [install documentation](installation.md)).

Normalizer and serializer are both activated with the bundle.

## Normalizer

Every object that implements behavior "normalizable" has to implement the following interface : `Majora\Framework\Normalizer\Model\NormalizableInterface`.

3 methods are required :

 - `static getScopes() : array` : returns object scopes as a map `name => fields`, see below
 - `normalize($scope) : array` : trigger object normalization for given scope and returns an array with data
 - `denormalize(array $data)` : hydrate this object with given data, if mappable

The normalizer component provides a Php trait to help implementation : `Majora\Framework\Normalizer\Model\NormalizableTrait`, but you can implement your own strategy for one or more entities.

### Normalization

Normalization begins with the `getScope()` method, which is the normalization configuration entry point.

Overview of all configuration options.

#### Scope configuration reference
```php

use Majora\Framework\Normalizer\Model\NormalizableInterface;
use Majora\Framework\Normalizer\Model\NormalizableTrait;

class Entity implements NormalizableInterface
{
    use NormalizableTrait;

    // ...
    public static function getScopes()
    {
        return array(

            // normalization on a single field
            'id' => 'id',   // define a scope as a string will output only this field
                            // at normalization, without an array wrapper

            // normalize scalar fields
            'simple' => array(                     // will output an array with those fields
                'id', 'field1', 'field2'           // as key, and fields values as values
            ),

            // null normalization rule
            'without_null' => array(               // using "?" into field name will mark it as optionnal
                'id', 'field1', 'field2?'          // if value is null, field will not be displayed at all
            ),

            // normalize sub objects
            'all' => array(                        // will output scalars as usual, and sub objects
                'id', 'field1', 'field2',          // at "default" scope for the first, and at "simple"
                'subObject1', 'subObject2@simple'  // one for the second
            ),

            // other scopes inclusion
            'composed' => array(                // will output all fields defined in "all" scope, plus
                '@all',                         // field3 field, and will override subObject1
                'field3', 'subObject1@simple'   // scoping with "simple" scope instead of default one
            )
        );
    }
}
```

Note the important operator : "@". It materializes a scope of a related object if used with a field name, or current object other scope.

You can easily customize all your object representations, function of many cases, to optimize data exposition, function of domain, security, or all your custom rules.

About field naming : Majora Normalizer use the Symfony PropertyAccess component to guess values from fields, so you can use direct property call or getter call, just the same as you do with PropertyAccess (into Form for example).

Particular cases :

 - DateTime objects : by default, normalize a date will output a formatted string, from `\DateTime::ISO8601` format
 - StdClass objects : normalize as array using a cast

#### Example
Consider these object definitions :
```php
class Article implements Normalizable
{
    protected $title = 'MajoraFrameworkExtraBundle documentation released';
    protected $headline = 'More than a year after release, MajoraFramework got a proper documentation !';
    protected $body = 'That\'s amazing, everyone wanted a documentation of this tools package, and now this is done. All the team worked very hard to reach this goal, and i would personally thanks my mom who learned me to always push myself off limits !';
    protected $category;

    public function __construct()
    {
        $this->category = new Category();
    }

    public function getScope()
    {
        return array(
            'simple' => array('title', 'headline', 'body', 'category@name'),
            'full' => array('@simple', 'category@simple')
        );
    }
}

class Category implements Normalizable
{
    protected $name = 'News';
    protected $website;

    public function __construct()
    {
        $this->website = new Website();
    }

    public function getScope()
    {
        return array(
            'name' => 'name',
            'simple' => array('name', 'website@url')
        );
    }
}

class Website implements Normalizable
{
    protected $url = 'www.github.com/LinkValue/MajoraFrameworkExtraBundle';

    public function getScopes()
    {
        return array(
            'url' => 'url',
            'simple' => array('url')
        );
    }
}
```

Normalize calls :
```php
$article = new Article();

var_export($article->normalize('simple'));

// will output
array(
   'title' => 'MajoraFrameworkExtraBundle documentation released',
   'headline' => 'More than a year after release, MajoraFramework got a proper documentation !',
   'body' => 'That\'s amazing, everyone wanted a documentation of this tools package, and now this is done. All the team worked very hard to reach this goal, and i would personally thanks my mom who learned me to always push myself off limits !',
   'category' => 'News'
);

var_export($article->normalize('full'));

// will output
array(
   'title' => 'MajoraFrameworkExtraBundle documentation released',
   'headline' => 'More than a year after release, MajoraFramework got a proper documentation !',
   'body' => 'That\'s amazing, everyone wanted a documentation of this tools package, and now this is done. All the team worked very hard to reach this goal, and i would personally thanks my mom who learned me to always push myself off limits !',
   'category' => array(
        'name' => 'News',
        'website' => 'www.github.com/LinkValue/MajoraFrameworkExtraBundle'
   )
);
```

### Denormalization

Object denormalization follow the same logic : an interface behavior, implemented into a Php trait.

On the other hand, denormalization doesn't use `getScope()` method, but mutators type hinting.
Why implementing a denormalization by this way ? Because an object member which is not accessible throught a mutator shouldn't be accessible throught denormalization.

With the same object structure :
```php
class Article implements NormalizableInterface
{
    use NormalizableTrait;

    protected $title;
    protected $category;

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function setCategory(Category $category)
    {
        $this->category = $category;

        return $this;
    }
}

class Category implements NormalizableInterface
{
    use NormalizableTrait;

    protected $name;

    public function setName($name)
    {
        $this->name = $name;
    }
}
```
With this call,
```php
$article = new Article();
$article->denormalize(array(
    'title' => 'MajoraFrameworkExtraBundle documentation released',
    'category' => array(
        'name' => 'News'
    )
));
```
normalizer will set scalar directly with PropertyAccessor component, and recurse on a "Category" object denormalization, because mutator defined a hinting on this class.

If you don't have an instanciated object, you can use directly the normalizer class, which can create it :
```php
$article = MajoraNormalizer::createNormalizer()->denormalize(
    array(
        'title' => 'MajoraFrameworkExtraBundle documentation released',
        'category' => array(
            'name' => 'News'
        )
    ),
    Article::class
);
```

This is the proper way to denormalize entity using constructor parameters (Value objects / DDD), because Majora Normalizer detects construct parameters as well.

### Service exposition

The normalizer class is registered into the DIC under **"majora.normalizer"**, and expose :
```php
/**
 * Normalize given object, following given scope, if object is a Normalizable
 *
 * @param object $object
 * @param string $scope
 *
 * @return array  array representation of given object
 */
public function normalize($object, $scope = 'default') : array;

/**
 * Denormalize given data into an object, given one,
 * or a created one if "normalizable" is a class name
 *
 * @param array|scalar  $data         raw data to denormalize
 * @param object|string $normalizable object to denormalize in, or object class name for instanciation
 *
 * @return object
 */
public function denormalize($data, $normalizable) : object;

```

## Serializer

Majora Serializer is an adapter for Normalizer : instead of returning raw data from normalizer, it transforms data to string, in various formats (xml, json, ...)

### Serialization

Serializer follows the same prototypes as normalizer, it use the [same scoping system](#scope-configuration-reference) as well.

```php
var_export(
    $serializer->serialize(
        new Article(),                  // object to serialize
        'json',                         // output format
        array('scope' => 'full')        // option set, "scope" is defined at "default" if not set
    )
);

// will output
"{
   "title": "MajoraFrameworkExtraBundle documentation released",
   "headline": "After more than a year after release, MajoraFramework got a proper documentation !",
   "body": "That\"s amazing, everyone wanted a documentation of this tools package, and now this is done. All the team worked very hard to reach this goal, and i would personally thanks my mom who learned me to always push myself off limits !",
   "category": {
        "name": "News",
        "website": "www.github.com/LinkValue/MajoraFrameworkExtraBundle"
   }
}"
```

### Deserialization

Same thing for deserialization mechanics :

```php
var_dump(
    $serializer->deserialize(
        "{
           "title": "MajoraFrameworkExtraBundle documentation released",
           "headline": "After more than a year after release, MajoraFramework got a proper documentation !",
           "body": "That\"s amazing, everyone wanted a documentation of this tools package, and now this is done. All the team worked very hard to reach this goal, and i would personally thanks my mom who learned me to always push myself off limits !",
           "category": {
                "name": "News",
                "website": "www.github.com/LinkValue/MajoraFrameworkExtraBundle"
           }
        }",                      // data to deserialize
        Article::class,          // output class
        'json'                   // input format
    )
);

// will output
"Article #1234 {
    "name": "MajoraFrameworkExtraBundle documentation released"
    // .......
}"
```

### Format handlers

Majora Serializer implements himself some formats (json and yaml), and is open for extensions, throught FormatHandler strategy system.

To create your own one (csv maybe ?), implement `Majora\Framework\Serializer\Handler\FormatHandlerInterface` into your custom class, and reference it as a service with tag :
```xml
<tag name="majora.serialization_handler" format="csv" />
```

Now you can use serializer and normalizer to expose your entities views as csv format, with :
```php
$csv = $serializer->serialize(new Article(), 'csv', array('scope' => 'simple'));
```

### Service exposition

Majora Serializer is exposed under id "majora.serializer" into DIC, and is aliased on "serializer" too, so **it overrides Symfony default one**.

## Disclaimer and advises

This component has been designed before Symfony Serializer component came to maturity. This component doesn't claim to replace it, he's here to propose another way to normalization.

For advanced cases, like many subobjects types, into large collections, with Doctrine lazy calls etc... normalizer can be slow. By the way, why normalize all this stuff ? But that's not the topic. For those cases, we advise you to implement your custom logic, into `normalize()` method.
The reflection used by denormalization system is slow as well, when denormalizing a large set of data, with deep dependencies, so don't fear to implement manually some property access right into the `denormalize()` method.

## Roadmap
 - v1.3
    - More configurations, like serializer alias replacing
 - v2.x
    - Api changing, break tight coupling between all component interfaces classes and traits, less static calls
    - Adds a cache warming up logic to pre-compile objects reflection
    - Remove deprecations

## Credits

- Inspired by [JMSSerializerBundle](https://github.com/schmittjoh/JMSSerializerBundle)
