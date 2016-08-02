# Normalizer and serializer component

Those components are another approach for normalization (transform an object from/to standard classes or array) and serialization (object to / from strings).

They are inspired from main serializer Symfony component and JMSSerializer, but follow another purpose : an object would be able to normalize (and serialize) himself because in many cases, inject a service is not possible, or will break some SOLID recipies, or generate circular dependencies.

The second important feature of this normalizer is the scope notion. Same as "group" into Symfony Serializer, normalization scope is a set of fields which are returned into normalization form. The improvement come from the scope chaining feature : the scope is localized into the serialized entity itself, and is not propagated : you can define another one into sub object.
The goal is reducing object output weight, with less circular references, and more field exposition control.

## Normalizer

Every object which implements behavior "normalizable" has to implements corresponding interface : `Majora\Framework\Normalizer\Model\NormalizableInterface`.

3 methods are required :

 - `static getScopes() : array` : returns object scopes as a map `name => fields`, see below
 - `normalize($scope) : array` : trigger object normalization for given scope and returns an array with data
 - `denormalize(array $data)` : hydrate this object with given data, if mappable

The normalizer component provides a Php trait to help implementation : `Majora\Framework\Normalizer\Model\NormalizableTrait`, but you can implement your own strategy for one or more entities.

### Normalization

Normalization begins with `getScope()` method, which is normalization configuration entry point.

Let's take a tour of all configuration options.

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

Note the important operator : "@". It materialize a scope, of a related object if used with a field name, or current object other scope.

You can easily customize all your object representations, function of many cases, to optimize data exposition, function of domain, security, or all your custom rules.

About field naming : Majora Normalizer use Symfony PropertyAccess component to guess values from fields, so you can use direct property call or getter call, just the same as you do with PropertyAccess (into Form for example).

Particular cases :

 - DateTime objects : by default, normalize a date will output a formatted string, from `\DateTime::ISO8601` format
 - StdClass objects : normalize as array using a cast

#### Example
Consider these object definitions :
```php

class Article implements Normalizable
{
    protected $title = 'MajoraFrameworkExtraBundle documentation released';
    protected $headline = 'After more than a year after release, MajoraFramework got a proper documentation !';
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
// array(
//    'title' => 'MajoraFrameworkExtraBundle documentation released',
//    'headline' => 'After more than a year after release, MajoraFramework got a proper documentation !',
//    'body' => 'That\'s amazing, everyone wanted a documentation of this tools package, and now this is done. All the team worked very hard to reach this goal, and i would personally thanks my mom who learned me to always push myself off limits !',
//    'category' => 'News'
// );

var_export($article->normalize('full'));

// will output
// array(
//    'title' => 'MajoraFrameworkExtraBundle documentation released',
//    'headline' => 'After more than a year after release, MajoraFramework got a proper documentation !',
//    'body' => 'That\'s amazing, everyone wanted a documentation of this tools package, and now this is done. All the team worked very hard to reach this goal, and i would personally thanks my mom who learned me to always push myself off limits !',
//    'category' => array(
//         'name' => 'News',
//         'website' => 'www.github.com/LinkValue/MajoraFrameworkExtraBundle'
//    )
// );
```

### Denormalization

## Serializer

### Serialization

### Deserialization

