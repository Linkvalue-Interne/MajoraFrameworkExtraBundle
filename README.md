# MajoraFrameworkExtraBundle
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/4ba76532-49c8-448d-902f-9e037102b7d2/mini.png)](https://insight.sensiolabs.com/projects/beb6e229-e98c-4df6-a894-2586a64418cc) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/LinkValue/MajoraFrameworkExtraBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/LinkValue/MajoraFrameworkExtraBundle/?branch=master) [![Build Status](https://travis-ci.org/LinkValue/MajoraFrameworkExtraBundle.svg?branch=master)](https://travis-ci.org/LinkValue/MajoraFrameworkExtraBundle) [![Code Coverage](https://scrutinizer-ci.com/g/LinkValue/MajoraFrameworkExtraBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/LinkValue/MajoraFrameworkExtraBundle/?branch=master) [![Total Downloads](https://poser.pugx.org/majora/framework-extra-bundle/downloads)](https://packagist.org/packages/majora/framework-extra-bundle) [![Latest Stable Version](https://poser.pugx.org/majora/framework-extra-bundle/v/stable)](https://packagist.org/packages/majora/framework-extra-bundle) [![License](https://poser.pugx.org/majora/framework-extra-bundle/license)](https://packagist.org/packages/majora/framework-extra-bundle)

Provides extra classes and configurations for Symfony framework.
Helps to implements modern and clean architectures, like DDD, CQRS, Flux...

Features included :

* [Normalizer / Serializer](docs/normalizer.md)
* Validation bridges
* API clients
* Base implementations for Api and Admin controllers
* DDD / CQRS / Flux helpers traits and base classes (domains, actions, loaders, repositories)
* Form extensions and transformers
* Doctrine event optimizer
* Agnostic route generation
* Inflector
* Current date provider
* Log helpers

Features to come in v1.* :

* More documentation
* Cookbook
* More tests

Features to come in v2.0 :

* Middlewares into loaders query system
* Broadcastables events with wildarding listening
* JSON component
* DI tags for recurent configurations like logger / debug / validator / event dispatcher registering
* Deprecations removal

## Installation

See complete installation [here](docs/installation.md).

## License

This bundle is under the MIT license. See the complete license :

    LICENSE.md

## Contributing

This bundle is open to contributions, please follow this [documentation](https://github.com/LinkValue/MajoraFrameworkExtraBundle/blob/master/docs/contributing.md) and have fun !

## Credits

- [Quentin Cerny](https://github.com/Nyxis), [Link Value](http://link-value.fr/), and [all contributors](https://github.com/LinkValue/MajoraFrameworkExtraBundle/graphs/contributors)
