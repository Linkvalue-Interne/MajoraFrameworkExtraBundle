# Validation helpers

Validation system is often ignored with Symfony, no time for it, "not required for v0", esthetic feature...
But users make mistakes. All the time. Errors have to be considered as an application use case.

These helpers don't add killer features to framework, just developer experience improvements.

## Yaml file parsing

Maybe you read it before, but for us, annotations are bad. Link a class and its configurations breaks everything we believe in, when we use dependency injection elsewhere.

So we use Yaml for our config files.

By default, Symfony loads one validation Yaml file per bundle, here : `path/To/Bundle/Resources/config/validation.yml`.
So you have to put all your entity validation mappings into the same file. It can be ugly when you don't use one bundle per entity architecture.

To solve this minor trouble, MajoraFrameworkExtraBundles adds another path for your validation mapping files : `path/To/Bundle/Resources/config/validation/*.yml`. All your `.yml` files within this folder will be add to Validation component.

## ValidationException

Like exposed before, we assume error management have to be an application feature.
Validation should also be a part of your domain implementation, not only into forms.

On the other hand, this is not better to `return false;` or `return $errorCode;` in your domain code. Exceptions are designed for this case, even if Php, for many developers, they are the same as Fatal Errors.

We suggest to use a specific exception which can expose why system refused input data : `Majora\Framework\Validation\ValidationException`, with construct prototype :

 - entity : object which triggered the error
 - report : error list, handles :
    - `ConstraintViolationListInterface` from `ValidatorInterface::validate()`
    - `FormErrorIterator` from `FormInterface::getErrors()`
    - simple array or `ArrayAccessInterface` `'field' => 'error as string'` indexed
 - groups : validation groups which have failed
 - code : Exception original prototype
 - previous : Exception original prototype

You can get all this stuff using accessors on the exception.

## Exception listener

Having an exception for validation is good, but handling it in every controller is painfull.
A first response of this problem is implementing an exception listener, which can format your exception to be readable for humans.

For enable it :
```yml
# app/config/config.yml

majora_framework_extra:
    # ...
    exception_listener:
        enabled: true
```

__**Note**__ : at the moment, it only is triggered by `application/json` content types and outputs `JsonResponse`.
