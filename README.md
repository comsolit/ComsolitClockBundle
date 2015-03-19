# Clock Bundle for Symfony

[![Build Status](https://travis-ci.org/comsolit/ComsolitClockBundle.svg?branch=master)](https://travis-ci.org/comsolit/ComsolitClockBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/comsolit/ComsolitClockBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/comsolit/ComsolitClockBundle/?branch=master)

Unit-Testing anything related to time is hard if the code under test directly
accesses the system time. Therefor this bundle provides a clock service
wrapping a DateTime instance that represents "now".

Another nice property is, that "now" remains the same during one request.

## Usage Examples

The clock also contains some helper functions that help to create easily
understandable code related to time.

### Test, whether a user may try to login again

After some failures to login the user from a given IP address may not login
for some minutes:

```PHP
$clock->hasElapsed($blockingPeriod, $lastFailedLogin->getDateTime())
```

Without the helper methods this would look like:

```PHP
((int)$lastFailedLogin->getDateTime()->format('U')) + $blockingPeriod >= $clock->getTimestamp()
```

### Test, whether a token is still valid

```PHP
$clock->isExpired($tokenExpirationTime)
```

### Save a file with time as part of the filename

```PHP
$fileName = $clock->getFileName() . '_datadump';
// e.g. $fileName === '2015-03-25_23-12-59_datadump'

```

## Helper methods in class Clock

```PHP
class Clock
{
    // SNIPP some stuff

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
    public function getSecondsSince(\DateTimeInterface $past)
    {
        return $this->getSeconds() - (int)$past->format('U');
    }

    /**
     * @return int
     */
    public function getSecondsUntil(\DateTimeInterface $future)
    {
        return - $this->getSecondsSince($future);
    }

    /**
     * @return bool
     * @param int $seconds
     */
    public function hasElapsed($seconds, \DateTimeInterface $since)
    {
        return $this->getSecondsSince($since) > $seconds;
    }

    /**
     * @return bool
     */
    public function isExpired(\DateTimeInterface $expiryDate)
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

    /**
     * Returns an instance of DateTimeImmutable for the given implementation of DateTimeInterface.
     *
     * This method should rather exist in the DateTimeImmutable class but Derick Rethans doesn't
     * think so.
     *
     * @param \DateTimeInterface $datetime
     * @return \DateTimeImmutable
     */
    public static function createDateTimeImmutable(\DateTimeInterface $datetime)
    {
        if ($datetime instanceof \DateTimeImmutable)
        {
            return $datetime;
        }

        return \DateTimeImmutable::createFromMutable($datetime);
    }
}

```