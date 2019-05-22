<?php

namespace Indigerd\Tolerance\Fallback\Strategy;

use Indigerd\Tolerance\Fallback\FallbackInterface;

class Fixture implements FallbackInterface
{
    protected $fixture;

    public function __construct($fixture)
    {
        $this->fixture = $fixture;
    }

    public function request(callable $requestAction)
    {
        try {
            $result = $requestAction();
            return $result;
        } catch (\Throwable $e) {
            return $this->fixture;
        }
    }
}
