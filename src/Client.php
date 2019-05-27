<?php

namespace Indigerd\Tolerance;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Response;
use Indigerd\Tolerance\Fallback\FallbackFactory;
use Indigerd\Tolerance\Fallback\FallbackInterface;

class Client
{
    protected $client;

    protected $fallbackFactory;

    protected $responseFactory;

    protected $defaultFallback;

    protected $fallbackConfig = [];

    public function __construct(
        HttpClient $client,
        FallbackFactory $fallbackFactory,
        ResponseFactory $responseFactory,
        $defaultFallback = 'retry',
        array $fallbackConfig = []
    ) {
        $this->client = $client;
        $this->fallbackFactory = $fallbackFactory;
        $this->responseFactory = $responseFactory;
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
            throw new \InvalidArgumentException('Invalid fallback strategy');
        }
        return $fallbackStrategy;
    }

    public function request(string $method, string $uri = '', array $options = [], $fallbackStrategy = null) : Response
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
        $result = $fallbackStrategy->request($requestAction);
        if (!($result instanceof Response)) {
            $result = $this->responseFactory->create(200, [], $result);
        }
        return $result;
    }
}
