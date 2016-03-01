<?php

namespace Majora\Framework\Model;

/**
 * Implements many temporal functions and DateTime helpers
 */
trait TimedTrait
{
    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * Proxy to return given date if defined, formated under given format (if defined)
     *
     * @param \DateTime $date
     * @param string    $format  optional format
     *
     * @return \DateTime string
     */
    protected function formatDateTime(\DateTime $date = null, $format = null)
    {
        return $date && $format ?
            $date->format($format) :
            $date
        ;
    }

    /**
     * Returns object created at.
     *
     * @param string $format optional date format
     *
     * @return \DateTime|string
     */
    public function getCreatedAt($format = null)
    {
        return $this->formatDateTime(
            $this->createdAt,
            $format
        );
    }

    /**
     * Define object created at.
     *
     * @param \DateTime $createdAt
     *
     * @return self
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Returns object updated at.
     *
     * @param string $format optional date format
     *
     * @return \DateTime|string
     */
    public function getUpdatedAt($format = null)
    {
        return $this->formatDateTime(
            $this->updatedAt,
            $format
        );
    }

    /**
     * Define object updated at.
     *
     * @param \DateTime $updatedAt
     *
     * @return self
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
