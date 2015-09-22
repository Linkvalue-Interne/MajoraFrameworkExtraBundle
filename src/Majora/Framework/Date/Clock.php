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
    protected $mockedDate;

    /**
     * use given date as mock
     *
     * @param string|\DateTime $date
     */
    protected function mock($date)
    {
        $this->mockedDate = $date instanceof \DateTime ?
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
        $date = $this->mockedDate instanceof \DateTime ?
            $this->mockedDate :
            new \DateTime()
        ;

        return empty($format) ?
            $date :
            $date->format($format)
        ;
    }
}
