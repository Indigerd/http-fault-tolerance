Fault Tolerant HTTP Requests
==========

Usage
-----

Example of simple usage (by default when http request will fail library will make 1 more attempt to make http request)

```php

use Indigerd\Tolerance\Client;
use GuzzleHttp\Client as HttpClient;
use Indigerd\Tolerance\Fallback\FallbackFactory;
use Indigerd\Tolerance\ResponseFactory;

$client = new Client(new HttpClient(), new FallbackFactory(), new ResponseFactory());
$response = $client->request('GET', 'http://facebook.com');
echo($response->getBody());

```

Example of using with Complex fallback strategy. Complex fallback strategy is a collection of fallbacks that will be called untill one will succeed.

```php

use Indigerd\Tolerance\Client;
use GuzzleHttp\Client as HttpClient;
use Indigerd\Tolerance\Fallback\FallbackFactory;
use Indigerd\Tolerance\ResponseFactory;

$client = new Client(new HttpClient(), new FallbackFactory(), new ResponseFactory());
$strategy = new \Indigerd\Tolerance\Fallback\Strategy\Complex(
    ['retry', new \Indigerd\Tolerance\Fallback\Strategy\Fixture('GGGGGG')],
    new FallbackFactory()
);
$response = $client->request('GET', 'http://google344534534534534534111111.com', [], $strategy);
echo($response->getBody());

```