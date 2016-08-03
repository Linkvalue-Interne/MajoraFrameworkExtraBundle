# MajoraFrameworkExtraBundle
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/4ba76532-49c8-448d-902f-9e037102b7d2/mini.png)](https://insight.sensiolabs.com/projects/beb6e229-e98c-4df6-a894-2586a64418cc) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/LinkValue/MajoraFrameworkExtraBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/LinkValue/MajoraFrameworkExtraBundle/?branch=master) [![Build Status](https://travis-ci.org/LinkValue/MajoraFrameworkExtraBundle.svg?branch=master)](https://travis-ci.org/LinkValue/MajoraFrameworkExtraBundle) [![Code Coverage](https://scrutinizer-ci.com/g/LinkValue/MajoraFrameworkExtraBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/LinkValue/MajoraFrameworkExtraBundle/?branch=master) [![Total Downloads](https://poser.pugx.org/majora/framework-extra-bundle/downloads)](https://packagist.org/packages/majora/framework-extra-bundle) [![Latest Stable Version](https://poser.pugx.org/majora/framework-extra-bundle/v/stable)](https://packagist.org/packages/majora/framework-extra-bundle) [![License](https://poser.pugx.org/majora/framework-extra-bundle/license)](https://packagist.org/packages/majora/framework-extra-bundle)

Provides extra classes and configurations for Symfony framework.
Helps to implements modern and clean architectures, like DDD, CQRS, Flux...

Features included :

* [Normalizer / Serializer](docs/normalizer.md)
* [Form extensions and transformers](docs/forms.md)
* [Inflector](docs/inflector.md)
* [Date mocker](docs/date_mocker.md)
* [Agnostic route generation](docs/agnostic_router.md)
* [API clients](docs/api_clients.md)
* [Validation helpers](docs/validation.md)
* [Log helpers](docs/logs.md)
* [Admin and Api controllers helpers](docs/controllers.md)
* Architecture helpers :
  * Entity loading system
  * Action system

Features to come in v1.* :

* More documentation
* Cookbook
* More tests

Features to come in v2.0 :

* Refactor folders structure
* Rework DDD helper classes naming to match original concepts
* Middlewares into loaders query system
* Broadcastables events with wildarding listening
* Normalization / Serialization strategies from loader delegates
* JSON component
* DI tags for recurent configurations like logger / debug / validator / event dispatcher registering
* Deprecations removal

## Installation

See complete installation [here](docs/installation.md).

## License

This bundle is under the MIT license. See the complete license :

    LICENSE.md

## Contributing

This bundle is open to contributions, please follow this [documentation](docs/contributing.md) and have fun !

## Credits

- [Quentin Cerny](https://github.com/Nyxis), [Link Value](http://link-value.fr/), and [all contributors](https://github.com/LinkValue/MajoraFrameworkExtraBundle/graphs/contributors)
