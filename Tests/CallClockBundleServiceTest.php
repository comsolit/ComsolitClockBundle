<?php

namespace Comsolit\ClockBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Comsolit\ClockBundle\ClockFactory;

class CallClockBundleServiceTest extends WebTestCase
{
    public function testServiceUsage()
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        $requestStack = $container->get('request_stack');
        $request = Request::create('/something');
        $requestStack->push($request);

        $clock = $container->get('comsolit_request_clock');
        $futureSeconds = $clock->getSecondsSince(new \DateTimeImmutable('2000-01-01'));
        $this->assertGreaterThan(0, $futureSeconds);
        $this->assertInstanceOf('Comsolit\ClockBundle\Clock', $clock);
    }

    public function testServiceFactoryWithServerRequestTime()
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        $dt = new \DateTimeImmutable();
        $requestStack = $container->get('request_stack');
        $request = Request::create('/something', 'GET', [], [], [], ['REQUEST_TIME' => $dt->format('U')]);
        $requestStack->push($request);

        $clock = $container->get('comsolit_request_clock');
        $this->assertEquals($dt->format('U'), $clock->getSeconds());
    }

    public function testServiceFactoryWithoutServerRequestTime()
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        $dt = new \DateTimeImmutable();
        $requestStack = $container->get('request_stack');
        $request = Request::create('/something');
        $requestStack->push($request);

        $clock = $container->get('comsolit_request_clock');
        $this->assertGreaterThanOrEqual($dt->format('U'), $clock->getSeconds());
    }

    public function testServiceFactoryAlsoWorksWithoutRequest()
    {
        $factory = new ClockFactory(new RequestStack());
        $clock = $factory->createClock();
        $this->assertInstanceOf('Comsolit\ClockBundle\Clock', $clock);
    }
}