<?php

namespace Majora\Framework\Domain\Action\Dal;

use Majora\Framework\Api\Client\RestApiClient;
use Majora\Framework\Validation\ValidationException;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Base trait for Dal actions
 */
trait DalActionTrait
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * define event dispatcher.
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * define validator.
     *
     * @param ValidatorInterface $validator
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * assert given entity is valid on given scope.
     *
     * @param object       $entity
     * @param string|array $scope
     *
     * @throws ValidationException If given object is invalid on given scope
     */
    protected function assertEntityIsValid($entity, $scope = null)
    {
        if (!$this->validator) {
            throw new \BadMethodCallException(sprintf(
                'Method %s() cannot be used while validator is not configured.',
                __METHOD__
            ));
        }

        $scopes = $scope ? (array) $scope : null;

        $violationList = $this->validator->validate(
            $entity,
            null,
            $scopes
        );

        if (!count($violationList)) {
            return;
        }

        throw new ValidationException($entity, $violationList, $scopes);
    }

    /**
     * fire given event.
     *
     * @param string $eventName
     * @param Event  $event
     *
     * @throws \BadMethodCallException If any event dispatcher set
     */
    protected function fireEvent($eventName, Event $event)
    {
        if (!$this->eventDispatcher) {
            throw new \BadMethodCallException(sprintf(
                'Method %s() cannot be used while event dispatcher is not configured.',
                __METHOD__
            ));
        }

        $this->eventDispatcher->dispatch($eventName, $event);
    }
}
