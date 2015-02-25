<?php

namespace Comsolit\ClockBundle;

class ClockTest extends \PHPUnit_Framework_TestCase
{
    const TIMEZONE = 'Europe/Zurich';

    private $clock;
    private $timestamp;
    private $tz;
    private $savedTimezone;

    public function setUp()
    {
        $this->savedTimezone = date_default_timezone_get();
        date_default_timezone_set(self::TIMEZONE);

        $this->tz = new \DateTimeZone(self::TIMEZONE);

        $dateTime = new \DateTime('now');
        $this->timestamp = (int)$dateTime->format('U');
        $this->clock = new Clock($dateTime);
    }

    public function tearDown()
    {
        date_default_timezone_set($this->savedTimezone);
    }

    /**
     * @test
     */
    public function getSecondsIsNumeric()
    {
        $this->assertInternalType('integer', $this->clock->getSeconds());
        $this->assertEquals($this->timestamp, $this->clock->getSeconds());
    }

    /**
     * @test
     */
    public function getSecondsSince()
    {
        $yesterday = new \DateTime('yesterday', $this->tz);
        $this->assertGreaterThan(20 * 3600, $this->clock->getSecondsSince($yesterday));
        $this->assertLessThan(49 * 3600, $this->clock->getSecondsSince($yesterday));

        $theAnswer = new \DateTime('@'.($this->timestamp - 42), $this->tz);
        $this->assertEquals(42, $this->clock->getSecondsSince($theAnswer));
    }

    /**
     * @test
     */
    public function hasElapsed()
    {
        $yesterday = new \DateTime('yesterday', $this->tz);
        $this->assertFalse($this->clock->hasElapsed(49 * 3600, $yesterday));
        $this->assertTrue($this->clock->hasElapsed(60, $yesterday));

        $theAnswer = new \DateTime('@'.($this->timestamp - 42), $this->tz);
        $this->assertFalse($this->clock->hasElapsed(43, $theAnswer));
        $this->assertTrue($this->clock->hasElapsed(41, $theAnswer));
    }

    /**
     * @test
     */
    public function getDateTime()
    {
        $this->assertInstanceOf('DateTimeImmutable', $this->clock->getDateTime());
        $this->assertEquals(
            $this->timestamp,
            (int)$this->clock->getDateTime()->format('U')
        );
    }

    /**
     * @test
     */
    public function createDateTime()
    {
        $this->assertInstanceOf('DateTime', $this->clock->createDateTime('today'));
        $this->assertInstanceOf('DateTime', $this->clock->createDateTime('@1234567890'));
        $this->assertEquals(self::TIMEZONE, $this->clock->createDateTime('today')->getTimezone()->getName());
    }

    /**
     * @test
     */
    public function isExpired()
    {
        $yesterday = new \DateTime('yesterday', $this->tz);
        $tomorrow = new \DateTime('+1day', $this->tz);
        $this->assertTrue($this->clock->isExpired($yesterday));
        $this->assertFalse($this->clock->isExpired($tomorrow));
    }

    /**
     * @test
     */
    public function instantiateWithDateTimeImmutable()
    {
        $dt = new \DateTimeImmutable();
        $instance = new Clock($dt);
        $this->assertSame($dt, $instance->getDateTime());
    }
}