<?php

namespace Majora\Framework\Domain\Action;

use Majora\Framework\Api\Client\RestApiClient;
use Majora\Framework\Validation\ValidationException;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Base trait for actions.
 */
trait ActionTrait
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
     * @var RestApiClient
     */
    private $restClient;

    /**
     * @var SerializerInterface
     */
    private $serializer;

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
     * define rest client.
     *
     * @param RestApiClient $restClient
     */
    public function setRestApiClient(RestApiClient $restClient)
    {
        $this->restClient = $restClient;
    }

    /**
     * returns api client if defined
     *
     * @return RestApiClient
     */
    protected function getRestApiClient()
    {
        if (!$this->restClient) {
            throw new \BadMethodCallException(sprintf(
                'Method %s() cannot be used while rest api client is not configured.',
                __METHOD__
            ));
        }

        return $this->restClient;
    }

    /**
     * define serializer.
     *
     * @param SerializerInterface $serializer
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * returns serializer if defined
     *
     * @return SerializerInterface
     */
    protected function getSerializer()
    {
        if (!$this->serializer) {
            throw new \BadMethodCallException(sprintf(
                'Method %s() cannot be used while serializer is not configured.',
                __METHOD__
            ));
        }

        return $this->serializer;
    }

    /**
     * assert given entity is valid on given scope.
     *
     * @param object $entity
     * @param string $scope
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

        $violationList = $this->validator->validate(
            $entity,
            null,
            $scope ? (array) $scope : null
        );

        if (!count($violationList)) {
            return;
        }

        throw new ValidationException(
            $entity,
            $violationList,
            $scope ? (array) $scope : null
        );
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
