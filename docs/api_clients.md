# API clients

Consuming webservices is a recurrent job on server-side development these days.
And over and over again, we implement our client, using Guzzle (or other library), everytime better, but everytime different.

API client component helps implementation of your calls to http webservices : it give you some classes to exploit Guzzle, Symfony Routing and Majora Agnostic router, in a query builder API style.

## Implementation

This component works around two classes :

 - a request factory, based on Symfony routes
 - a bridge between factory and a Guzzle Http client

First, create a `RestApiRequestFactory`, with a map `alias => route_name`, and a reference of an UrlGenerator.
Every alias will be the name of the future request.

## Example

For example, we want to read and write tweets from Twitter API (non-contractual endpoint).

First, we have to create read and write tweets routes :
```yml
# app/config/routing.yml
twitter_api:
    resource: routing/twitter_api.yml
    prefix: /tweets
    host: %twitter_api_host%
    scheme: https

# app/config/routing/twitter_api.yml
twitter_api_read_tweets:
    path: /{user}

twitter_api_create_tweet:
    path: /
```

Then we create instances of component classes, through DIC :
```xml
<!-- MajoraTwitterBundle/Resources/config/services.xml -->
<container>
    <services>
        <service id="majora.twitter.api_client" class="Majora\Framework\Api\Client\RestApiClient">
            <argument type="service">
                <service class="GuzzleHttp\Client" /><!-- custom here this Client -->
            </argument>
            <argument type="service">
                <service class="Majora\Framework\Api\Request\RestApiRequestFactory"
                    parent="majora.http.abstract_request_factory"> <!-- parent service to help configuration -->
                    <call method="registerRouteMapping">
                        <argument type="collection">
                            <argument key="read">twitter_api_read_tweets</argument>
                            <argument key="write">twitter_api_create_tweet</argument>
                        </argument>
                    </call>
                </service>
            </argument>
        </service>
    </services>
</container>
```

Now, we can get an user tweet list from this call :
```php
$response = $this->container->get('majora.twitter.api_client')->send(
    'read',                         // request name, route key into factory
    'GET',                          // method
    array('user' => 'link-12345'),  // route query parameter
    array(),                        // data
    array()                         // Guzzle request options
);
```

__**Note**__ : `"majora.http.abstract_request_factory"` defines a dependency on [Majora Agnostic Router](agnostic_router.md) to always strip front controller file from url generation.

## Cookbook

Example above is NOT good enough. With all Symfony and MajoraFramework, we can do better.

### Introduce proper methods for calling clients

```php
// MajoraTwitterBundle/Twitter/TwitterApiClient.php

use Majora\Framework\Api\Client\RestApiClient;

class TwitterApiClient extends RestApiClient
{
    public function retrieveTweets(User $user)
    {
        return (string) $this
            ->send('read', 'GET', array('user' => $user->getTwitterId())
            ->getBody()
        ;
    }
}
```
```xml
<!-- MajoraTwitterBundle/Resources/config/services.xml -->
<container>
    <services>
        <service id="majora.twitter.api_client" class="MajoraTwitterBundle/Twitter/TwitterApiClient">
            <argument type="service">
                <service class="GuzzleHttp\Client" />
            </argument>
            <argument type="service">
                <service class="Majora\Framework\Api\Request\RestApiRequestFactory"
                    parent="majora.http.abstract_request_factory">
                    <call method="registerRouteMapping">
                        <argument type="collection">
                            <argument key="read">twitter_api_read_tweets</argument>
                            <argument key="write">twitter_api_create_tweet</argument>
                        </argument>
                    </call>
                </service>
            </argument>
        </service>
    </services>
</container>
```

### Parse responses into Php object using [serializer](normalizer.md)
```php
// MajoraTwitterBundle/Twitter/TwitterApiClient.php

use MajoraTwitterBundle\Entity\TweetCollection;
use Majora\Framework\Api\Client\ApiClientInterface;
use Symfony\Component\Serializer\SerializerInterface;

class TwitterApiClient
{
    protected $restApiClient;

    protected $serializer;

    public function __construct(ApiClientInterface $restApiClient, SerializerInterface $serializer)
    {
        $this->restApiClient = $restApiClient;
        $this->serializer = $serializer;
    }

    public function retrieveTweets(User $user)
    {
        $response = $this->restApiClient
            ->send('read', 'GET', array('user' => $user->getTwitterId())
        ;

        return $this->serializer->deserialize(
            (string) $response->getBody(),
            TweetCollection::class,
            'json'
        );
    }
}
```
```xml
<!-- MajoraTwitterBundle/Resources/config/services.xml -->
<container>
    <services>
        <service id="majora.twitter.api_client" class="MajoraTwitterBundle/Twitter/TwitterApiClient">
            <argument type="service">
                <service class="Majora\Framework\Api\Client\RestApiClient">
                    <argument type="service">
                        <service class="GuzzleHttp\Client" />
                    </argument>
                    <argument type="service">
                        <service class="Majora\Framework\Api\Request\RestApiRequestFactory"
                            parent="majora.http.abstract_request_factory">
                            <call method="registerRouteMapping">
                                <argument type="collection">
                                    <argument key="read">twitter_api_read_tweets</argument>
                                    <argument key="write">twitter_api_create_tweet</argument>
                                </argument>
                            </call>
                        </service>
                    </argument>
                </service>
            </argument>
            <argument type="service" id="majora.serializer" />
        </service>
    </services>
</container>
```

## Roadmap

Obviously, developer experience of this component is not the best ever.
Our bigger evolution plans are :

 - v2.* :
    - debug features, with toolbar integration
    - dynamic http client creation
    - use Guzzle middlewares
    - query builder system instead of huge set of optionnal parameters
    - hard reduce service structure, using factories / tags / decorators

