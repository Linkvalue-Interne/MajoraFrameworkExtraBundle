<?php

namespace Majora\Framework\Serializer;

use Majora\Framework\Serializer\Handler\FormatHandlerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Base class for fixtures repository.
 *
 * @group legacy
 */
class MajoraSerializer implements SerializerInterface
{
    /**
     * FormatHandlerInterface[].
     */
    protected $handlers;

    /**
     * construct.
     *
     * @param FormatHandlerInterface[] $handlers
     */
    public function __construct(array $handlers)
    {
        $this->handlers = array_map(
            function (FormatHandlerInterface $handler) { return $handler; },
            $handlers
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see SerializerInterface::serialize()
     */
    public function serialize($data, $format, array $context = array())
    {
        if (!isset($this->handlers[$format])) {
            throw new \BadMethodCallException(sprintf(
                'Unsupported format "%s", only [%s] are',
                $format, implode(', ', array_keys($this->handlers))
            ));
        }

        return $this->handlers[$format]->serialize(
            $data,
            empty($context['scope']) ? 'default' : $context['scope']
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see SerializerInterface::deserialize()
     */
    public function deserialize($data, $type, $format, array $context = array())
    {
        if (!isset($this->handlers[$format])) {
            throw new \BadMethodCallException(sprintf(
                'Unsupported format "%s", only [%s] are',
                $format, implode(', ', array_keys($this->handlers))
            ));
        }

        return $this->handlers[$format]->deserialize($data, $type);
    }
}
