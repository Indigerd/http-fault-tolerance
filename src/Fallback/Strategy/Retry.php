<?php

namespace Indigerd\Tolerance\Fallback\Strategy;

use Indigerd\Tolerance\Fallback\FallbackInterface;

class Retry implements FallbackInterface
{
    protected $attempts = 1;

    protected $delays = [];

    public function __construct(int $attempts = 1, array $delays = [])
    {
        $this->attempts = $attempts;
        $this->delays = $delays;
    }

    public function request(callable $requestAction)
    {
        $i = 0;
        $lastException = new \RuntimeException('Request failed');
        while ($i <= $this->attempts) {
            try {
                $result = $requestAction();
                return $result;
            } catch (\Throwable $e) {
                $lastException = $e;
                $i++;
                if (isset($this->delays[$i])) {
                    usleep($this->delays[$i]);
                }
            }
        }
        throw $lastException;
    }
}
