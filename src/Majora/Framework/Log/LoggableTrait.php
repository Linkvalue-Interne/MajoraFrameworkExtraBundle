<?php

namespace Majora\Framework\Log;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

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
    public function registerLogger(LoggerInterface $logger = null, $debug = false)
    {
        $this->logger = $logger ?: new NullLogger();
        $this->debug = $debug;

        return $this;
    }
}
