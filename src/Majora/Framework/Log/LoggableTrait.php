<?php

namespace Majora\Framework\Log;

use Psr\Log\LoggerInterface;

/**
 * Provides an accessor to logger and debug mode
 */
trait LoggableTrait
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var boolean
     */
    protected $debug;

    /**
     * register a logger and eventually debug mode into Loggable class
     *
     * @param  LoggerInterface $logger
     * @param  boolean         $debug
     *
     * @return self
     */
    public function registerLogger(LoggerInterface $logger, $debug = false)
    {
        $this->logger = $logger;
        $this->debug = $debug;

        return $this;
    }

    /**
     * @see LoggerInterface::log()
     */
    public function log($level, $message, array $context = array())
    {
        if (!$this->logger) {
            return $this;
        }

        $this->logger->log($level, $message, $context);

        return $this;
    }
}
