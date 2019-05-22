<?php

namespace Indigerd\Tolerance;

use GuzzleHttp\Client as HttpClient;
use Indigerd\Tolerance\Fallback\FallbackFactory;
use Indigerd\Tolerance\Fallback\FallbackInterface;

class Client
{
    protected $client;

    protected $fallbackFactory;

    protected $defaultFallback;

    protected $fallbackConfig = [];

    public function __construct(
        HttpClient $client,
        FallbackFactory $fallbackFactory,
        $defaultFallback = 'retry',
        array $fallbackConfig = []
    ) {
        $this->client = $client;
        $this->fallbackFactory = $fallbackFactory;
        $this->fallbackConfig = $fallbackConfig;
        if (!($defaultFallback instanceof FallbackInterface)) {
            $defaultFallback = $this->fallbackFactory->create($defaultFallback);
        }
        $this->defaultFallback = $defaultFallback;
    }

    public function request($method, $uri = '', array $options = [], $fallbackStrategy = null)
    {
        $requestAction = function () use ($method, $uri, $options) {
            $this->client->request($method, $uri, $options);
        };
        if (is_array($fallbackStrategy)) {
            $fallbackStrategy = $this->fallbackFactory->create(...$fallbackStrategy);
        }
        if (is_string($fallbackStrategy)) {
            $fallbackStrategy = $this->fallbackFactory->create($fallbackStrategy);
        }
        if (!($fallbackStrategy instanceof FallbackInterface)) {
            $fallbackStrategy = $this->defaultFallback;
        }
        return $fallbackStrategy->request($requestAction);
    }
}
