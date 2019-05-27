<?php

namespace Indigerd\Tolerance;

use GuzzleHttp\Psr7\Response;

class ResponseFactory
{
    public function create(
        $status = 200,
        array $headers = [],
        $body = null,
        $version = '1.1',
        $reason = null
    ) : Response {
        return new Response($status, $headers, $body, $version, $reason);
    }
}
