<?php

namespace Majora\Framework\Form\Extension\Json;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class JsonExtensionListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
            );
    }

    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        if (!is_string($data)) {
            throw new \InvalidArgumentException('Invalid argument: the submitted variable must be a string when you enable the json_format option.');
        }
        $baseData = $data;
        $data = @json_decode($data, true);
        if (null === $data) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid submitted json data, error %s : %s, json : %s',
                json_last_error(),
                json_last_error_msg(),
                $baseData
            ));
        }
        $event->setData($data);
    }
}
