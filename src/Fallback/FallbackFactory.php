<?php

namespace Indigerd\Tolerance\Fallback;

use Indigerd\Tolerance\Fallback\Strategy\Complex;
use Indigerd\Tolerance\Fallback\Strategy\Fixture;
use Indigerd\Tolerance\Fallback\Strategy\Ignore;
use Indigerd\Tolerance\Fallback\Strategy\Retry;

class FallbackFactory
{
    protected $strategies = [
        'retry' => Retry::class,
        'fixture' => Fixture::class,
        'complex' => Complex::class,
        'ignore' => Ignore::class
    ];

    public function create(string $strategy, array $config = [])
    {
        if (!isset($this->strategies[$strategy])) {
            throw new \InvalidArgumentException("Invalid fallback strategy: $strategy");
        }
        return new $this->strategies[$strategy](...$config);
    }
}
