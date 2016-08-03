# Inflector

When following coding standards, many times, changing string cases is awefull : camelcase, snake-case... At the moment, Symfony doesn't have a proper component which can handle string format conversion, so there is one !

It handles conversion from various format to all others.

## String formats

Supported transformations are :

 - camelize : `$inflector->camelize('this_string');` => `'thisString'`
 - pascalize : `$inflector->pascalize('this_string');` => `'ThisString'`
 - snakelize : `$inflector->snakelize('thisString');` => `'this_string'`
 - spinalize : `$inflector->spinalize('thisString');` => `'this-string'`
 - uppercase : `$inflector->uppercase('thisString');` => `'THIS_STRING'`
 - slugify : `$inflector->slugify('baguette Croissants pain d\'épice');` => `'baguette-croissants-pain-d-epice'`

This algorythms are based on Symfony Container [helper methods](https://github.com/symfony/dependency-injection/blob/master/Container.php#L342).

Every format can be called dynamically throught `normalize()` method :
```
$inflector('this_string', 'snakelize');
```
`normalize()` works with arrays too, and will iterate to format all keys.
Usefull for request data form submission, for example :
```php
    // ....
    $form->submit($inflector->normalize(
        $request->request->all(),
        'camelize'
    ));
```

## Directories formats

When working with filesystem, sometimes on Windows, you have to worry about DIRECTORY_SEPARATOR. Inflector do it, simple and smart, to simplify this tedious task : `$inflector->directorize('path/to/the/file');`.

It can also convert Windows system root to Unix one, with method `unixizePath('C:\home');` => `'/home'`.

## Service exposition

Inflector is registered into DIC under key "majora.inflector", but you can also use it alone, with `new Inflector();`.
