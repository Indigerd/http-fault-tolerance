<?php

namespace Indigerd\Tolerance\Fallback;

use Indigerd\Tolerance\Fallback\Strategy\Retry;

class FallbackFactory
{
    protected $strategies = [
        'retry' => Retry::class,
    ];

    public function create(string $strategy, array $config = [])
    {
        if (!isset($this->strategies[$strategy])) {
            throw new \InvalidArgumentException("Invalid fallback strategy: $strategy");
        }
        return new $this->strategies[$strategy](...$config);
    }
}