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
        $this->defaultFallback = $this->createFallback($defaultFallback);
    }

    protected function createFallback($fallbackStrategy) : FallbackInterface
    {
        if (is_array($fallbackStrategy)) {
            $fallbackStrategy = $this->fallbackFactory->create(...$fallbackStrategy);
        }
        if (is_string($fallbackStrategy)) {
            $fallbackStrategy = $this->fallbackFactory->create($fallbackStrategy);
        }
        if (!($fallbackStrategy instanceof FallbackInterface)) {
            throw new \InvalidArgumentException('Invalid falback strategy');
        }
        return $fallbackStrategy;
    }

    public function request($method, $uri = '', array $options = [], $fallbackStrategy = null)
    {
        $requestAction = function () use ($method, $uri, $options) {
            return $this->client->request($method, $uri, $options);
        };
        try {
            $fallbackStrategy = $this->createFallback($fallbackStrategy);
        } catch (\InvalidArgumentException $e) {
            if (isset($this->fallbackConfig[strtolower($method)][$uri])) {
                $fallbackStrategy = $this->createFallback($this->fallbackConfig[strtolower($method)][$uri]);
            }
        } finally {
            if (!($fallbackStrategy instanceof FallbackInterface)) {
                $fallbackStrategy = $this->defaultFallback;
            }
        }
        return $fallbackStrategy->request($requestAction);
    }
}
