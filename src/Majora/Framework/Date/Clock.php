<?php

namespace Majora\Framework\Date;

/**
 * Class which provide current date used to allow mocked dates
 * into domain layers instead of using current one
 */
class Clock
{
    /**
     * @var \DateTime
     */
    private $currentDate;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->currentDate = new \DateTime();
    }

    /**
     * use given date as mock
     *
     * @param string|\DateTime $date
     */
    protected function mock($date)
    {
        $this->currentDate = $date instanceof \DateTime ?
            $date :
            date_create($date)
        ;
    }

    /**
     * return current date
     *
     * @param string $format optionnal date format
     *
     * @return \DateTime
     */
    public function now($format = null)
    {
        $date = clone $this->currentDate;

        return empty($format) ?
            $date :
            $date->format($format)
        ;
    }
}
