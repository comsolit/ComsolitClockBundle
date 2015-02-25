<?php

namespace Comsolit\ClockBundle;

/**
 * Clock Service to represent a request constant, system wide instance in time.
 *
 * Having the clock as a service instead of using date() directly makes it
 * possible to easily change the system time in unit tests.
 */
class Clock
{
    /**
     * @var \DateTimeImmutable
     */
    private $now;

    public function __construct(/*\DateTimeInterface*/ $now)
    {
        $this->now = self::createDateTimeImmutable($now);
    }

    /**
     * Time representation usable as (part of) a file name, e.g.: 2014-05-23_13-45-23
     * @return string
     */
    public function getFileName()
    {
        return $this->now->format('Y-m-d_H-i-s');
    }

    /**
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->now;
    }

    /**
     * @return int
     */
    public function getSeconds()
    {
        return (int)$this->now->format('U');
    }

    /**
     * @return int
     */
    public function getSecondsSince(/*\DateTimeInterface*/ $past)
    {
        return $this->getSeconds() - (int)$past->format('U');
    }

    /**
     * @return int
     */
    public function getSecondsUntil(/*\DateTimeInterface*/ $future)
    {
        return - $this->getSecondsSince($future);
    }

    /**
     * @return bool
     * @param int $seconds
     */
    public function hasElapsed($seconds, /*\DateTimeInterface*/ $since)
    {
        return $this->getSecondsSince($since) > $seconds;
    }

    /**
     * @return bool
     */
    public function isExpired(/*\DateTimeInterface*/ $expiryDate)
    {
        return (int)$this->now->format('U') > (int)$expiryDate->format('U');
    }

    /**
     * Create new DateTime object with the timezone of this clock
     *
     * @param String $time
     */
    public function createDateTime($time)
    {
        return (new \DateTime($time, $this->now->getTimezone()))->setTimezone($this->now->getTimezone());
    }

    public static function createDateTimeImmutable(/*\DateTimeInterface*/ $datetime)
    {
        if ($datetime instanceof \DateTimeImmutable)
        {
            return $datetime;
        }

        return \DateTimeImmutable::createFromMutable($datetime);
    }
}
