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

    private function getRequestTime()
    {
        $masterRequest = $this->requestStack->getMasterRequest();
        if (is_null($masterRequest)) {
            return new \DateTimeImmutable();
        }

        if (!$masterRequest->server->has('REQUEST_TIME')) {
            return new \DateTimeImmutable();
        }

        return (new \DateTimeImmutable())->setTimestamp((int)$masterRequest->server->get('REQUEST_TIME'));
    }

    public function createClock()
    {
        return new Clock($this->getRequestTime());
    }
}