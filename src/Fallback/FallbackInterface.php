<?php

namespace Indigerd\Tolerance\Fallback;

interface FallbackInterface
{
    public function request(callable $requestAction);
}
