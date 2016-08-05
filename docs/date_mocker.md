# Clock

Working with date is boring : timezone, testing, ...
When following standard quality, DateTime object should be injected into services, but it seems to be overkill. Except if this is already implemented.

## Configuration

```yml
# app/config/config.yml

majora_framework_extra:
    # ...
    clock:
        enabled: true            # default false, enable / disable component
        mock_param: "_date_mock" # by default, mock parameter name into request and cli options
```

## Date injection

Majora Clock component adds a service under key "majora.clock", which provides a `Majora\Framework\Date\Clock` object.
One useful method : `now()`, returning current date, formatted if first parameter is used.

You can use this class into your "time-reactive" services, to increase testability, and to be decoupled to date.

Example :
```php

class DelayCalculator
{
    protected $clock;

    public function __construct(Clock $clock)
    {
        $this->clock = $clock;
    }

    public function getDelay(\DateTime $deliveryDate, $format = 'm d')
    {
        return $deliveryDate->diff($this->clock->now())
            ->format($format)
        ;
    }
}
```

It resolves the 1 sec problem, when time changes between many classes function of time which aren't called at same second : time is created at service construction and won't change.

## Date mocking system

Use a provider for current date allows mocking into unit tests, but in current testing ?

Clock component implements a magic parameter for request and a magic option for console commands to mock date directly from execution, to test a specified date.

Examples :
```sh
php bin/console article:publication_scheduler --_date_mock="2016-08-01"
```
```
GET /articles/published?_date_mock=2016-08-01
```
Current date into Clock will be mocked by given one.

## Roadmap

 - v1.3
    - Use DateTimeImmutable instead of cloning current date
