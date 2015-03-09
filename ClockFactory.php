<?php

namespace Comsolit\ClockBundle;

use Symfony\Component\HttpFoundation\RequestStack;

class ClockFactory
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function createClock()
    {
        $server = $this->requestStack->getMasterRequest()->server;
        $dt = $server->has('REQUEST_TIME')
            ? (new \DateTimeImmutable())->setTimestamp((int)$server->get('REQUEST_TIME'))
            : new \DateTimeImmutable();

        return new Clock($dt);
    }
}