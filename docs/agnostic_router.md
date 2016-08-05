# Agnostic router

You need sometimes to call some services in the cloud, Google APIs, Facebook... Potentially all APIs in the world.
Most of the time, these calls are HTTP calls, and require at least an url generation.
How do we generate this url ? `sprintf()`? `http_build_query()`? `implode()`?

No, there is a better way.

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
twitter_tweet_publication:       # external route
    path: /tweets
    host: api.twitter.com
    scheme: https
```
```php
    $url = $this->container->get('router')->generate('twitter_tweet_publication', array(
        'api_key' => $this->container->getParameter('twitter_api_key')
        'query_key' => 'query_value'
    ));

    // will output
    ""https://api.twitter.com/app_dev.php/tweets?api_key=123456azerty";

    $url = $this->container->get('majora.agnostic_url_generator')->generate('twitter_tweet_publication', array(
        'api_key' => $this->container->getParameter('twitter_api_key')
    ));

    // will output
    "https://api.twitter.com/tweets?api_key=123456azerty";
```

#### Roadmap

- v2.*
    - Integrate logic into standard routing generation with a route option to mute current RequestContext
