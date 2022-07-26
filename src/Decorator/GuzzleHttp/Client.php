<?php

namespace Indigerd\Tolerance\Decorator\GuzzleHttp;

use GuzzleHttp\Client as HttpClient;
use Indigerd\Tolerance\Client as Tolerance;
use Indigerd\Tolerance\Fallback\FallbackFactory;
use Indigerd\Tolerance\ResponseFactory;

class Client extends HttpClient
{
    protected $tolerance;

    public function __construct(
        array $config = [],
        $defaultFallback = 'retry',
        array $fallbackConfig = []
    ) {
        parent::__construct($config);
        $this->tolerance = new Tolerance(
            $this,
            new FallbackFactory,
            new ResponseFactory,
            $defaultFallback,
            $fallbackConfig
        );
    }

    public function request($method, $uri = '', array $options = [])
    {
        $result = $this->tolerance->request($method, $uri, $options);

    }
}
