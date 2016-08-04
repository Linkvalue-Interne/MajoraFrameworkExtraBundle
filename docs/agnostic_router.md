# Agnostic router

Sometimes, you need to call some services in the cloud, Google apis, Facebook... Potentially all Apis in the world.
Most of the time, these calls are HTTP calls, and require at least an url generation.
How do we generate this url ? `sprintf()`? `http_build_query()`? `implode()`?

No, there's a better way.

Symfony has a proper Router component, which can do this job, but a little problem remains : the `app_dev.php` into your dev environment, added by FrameworkBundle router bridge.

Agnostic router feature propose to solve this issue by proxying the standard `UrlGenerator` service, to always strip `app_dev.php` in generated url.
This service is referenced into DIC under key "majora.agnostic_url_generator".

#### Configuration
Agnostic router is activated from bundle configuration.

```yml
# app/config/config.yml

majora_framework_extra:
    # ...
    agnostic_url_generator:
        enabled: true
```


#### Example
```yml
# app/config/routing.yml
majora_article_edition:          # local route
    path: /articles/{id}/edit
    defaults:
        _controller: "MajoraAppBundle:Article:edit"
    methods: [GET, POST]
    requirements:
        id: \d+
    host: %majora_admin_host%

twitter_tweet_publication:       # external route
    path: /tweets
    host: api.twitter.com
    scheme: https
```
```php
    $url = $this->container->get('router')->generate('majora_article_creation', array(
        'id' => 42,
        'query_key' => 'query_value'
    ));

    // will output
    "//majora.dev/app_dev.php/articles/42/edit?query_key=query_value";

    $url = $this->container->get('majora.agnostic_url_generator')->generate('twitter_tweet_publication', array(
        'api_key' => $this->container->getParameter('twitter_api_key')
    ));

    // will output
    "https://api.twitter.com/tweets?api_key=123456azerty";
```

#### Roadmap

- v2.*
    - Integrate logic into standard routing generation with a route option to mute current RequestContext
