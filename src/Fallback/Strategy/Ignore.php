<?php

namespace Indigerd\Tolerance\Fallback\Strategy;

use Indigerd\Tolerance\Fallback\FallbackInterface;

class Ignore implements FallbackInterface
{
    public function request(callable $requestAction)
    {
        try {
            $result = $requestAction();
            return $result;
        } catch (\Throwable $e) {}
    }
}
