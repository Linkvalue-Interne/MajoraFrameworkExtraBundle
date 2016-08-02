# Installation

Use [Composer](http://getcomposer.org) !
You can pick a version at [Packagist](https://packagist.org/packages/majora/framework-extra-bundle), this bundle follows standard versioning : _dev-master_ for last updates, _~1.3_ for stable releases.

```js
// composer.json
{
    "require": {
        // ...
        "majora/framework-extra-bundle": "~1.3"
    }
}
```

Register the bundle into your Kernel :
```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Majora\Bundle\FrameworkExtraBundle\MajoraFrameworkExtraBundle($this),
    );
}
```
**Note** : Pay attention to the reference into bundle instanciation, it's required for iterate over bundles for extra configuration discovering.

##### Continue to [configurations reference](configuration_reference.md).
