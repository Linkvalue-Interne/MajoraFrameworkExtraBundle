# Configuration reference

```yml
# app/config/config.yml

majora_framework_extra:

    clock:                        # clock component
        enabled: false            # enable it or not
        mock_param: _date_mock    # mock parameter name (request param, console option)

    translations:
        locales: []               # enable locales (used for entity loading system)

    agnostic_url_generator:       #
        enabled: false            #
                                  #
    exception_listener:           #
        enabled: false            # components toggle switches
                                  # use it to disable unused features
    doctrine_events_proxy:        # to improve performence (hi Doctrine ORM),
        enabled: false            # or container compilation speed
                                  #
    json_form_extension:          #
        enabled: false            #
```
