<?php

namespace Comsolit\ClockBundle;

class Clock {

    const TIMEZONE = 'Europe/Zurich';
    private static $instance;

    private $now;

    public function __construct(\DateTime $now = null) {
        if(!$now instanceof \DateTime) {
            $now = new \DateTime('now', new \DateTimeZone(self::TIMEZONE));
        }
        $this->now = $now;
    }

    /**
     * Time representation usable as (part of) a file name, e.g.: 2014-05-23_13-45-23
     * @return string
     */
    public function getFileName() {
        return $this->now->format('Y-m-d_H-i-s');
    }

    /**
     * @return \DateTime
     */
    public function getDateTime() {
        return $this->now;
    }

    /**
     * @return int
     */
    public function getSeconds() {
        return (int)$this->now->format('U');
    }

    /**
     * @return int
     */
    public function getSecondsSince(\DateTime $past) {
        return $this->getSeconds() - (int)$past->format('U');
    }

    /**
     * @return int
     */
    public function getSecondsUntil(\DateTime $future) {
        return - $this->getSecondsSince($future);
    }

    /**
     * @return bool
     * @param int $seconds
     */
    public function hasElapsed($seconds, \DateTime $since) {
        return $this->getSecondsSince($since) > $seconds;
    }

    /**
     * @return bool
     */
    public function isExpired(\DateTime $expiryDate) {
        return $this->now > $expiryDate;
    }

    /**
     * Create new DateTime object with the timezone of this clock
     *
     * @param String $time
     */
    public function createDateTime($time) {
        return (new \DateTime($time, $this->now->getTimezone()))->setTimezone($this->now->getTimezone());
    }

    public static function getInstance() {
        if(!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * This function is only intended for testing
     */
    public static function _setInstance(Clock $clock) {
        self::$clock = $clock;
    }
}