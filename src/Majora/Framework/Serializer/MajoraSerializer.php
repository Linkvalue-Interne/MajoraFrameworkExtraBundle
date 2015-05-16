<?php

namespace Majora\Framework\Serializer;

use Symfony\Component\Serializer\SerializerInterface;

/**
 * Base class for fixtures repository.
 */
class MajoraSerializer
    implements SerializerInterface
{
    protected $handlers;

    /**
     * construct.
     *
     * @param array $handlers
     */
    public function __construct(array $handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
