<?php

namespace Indigerd\Tolerance\Fallback\Strategy;

use Indigerd\Tolerance\Fallback\FallbackFactory;
use Indigerd\Tolerance\Fallback\FallbackInterface;

class Complex implements FallbackInterface
{
    protected $strategies;

    public function __construct(array $strategies, FallbackFactory $fallbackFactory)
    {
        foreach ($strategies as $strategy) {
            if (!($strategy instanceof FallbackInterface)) {
                $strategy = $fallbackFactory->create($strategy);
            }
            $this->strategies[] = $strategy;
        }
    }

    public function request(callable $requestAction)
    {
        $lastException = new \RuntimeException('Request failed');
        foreach ($this->strategies as $strategy) {
            try {
                $result = $strategy->request($requestAction);
                return $result;
            } catch (\Throwable $e) {
                $lastException = $e;
            }
        }
        throw $lastException;
    }
}
