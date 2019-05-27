<?php

namespace Indigerd\Tolerance\Test;

use Indigerd\Tolerance\Fallback\Strategy\Fixture;
use PHPUnit\Framework\TestCase;
use Indigerd\Tolerance\Client;
use Indigerd\Tolerance\ResponseFactory;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Response;
use Indigerd\Tolerance\Fallback\FallbackFactory;
use Indigerd\Tolerance\Fallback\FallbackInterface;

class ClientTest extends TestCase
{
    protected $client;

    protected $fallbackFactory;

    protected $responseFactory;

    protected $httpClient;

    public function setUp(): void
    {
        $this->httpClient = $this->getMockBuilder(HttpClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();

        $this->responseFactory = $this->getMockBuilder(ResponseFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->fallbackFactory = $this->getMockBuilder(FallbackFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->client = new Client($this->httpClient, $this->fallbackFactory, $this->responseFactory);
    }

    protected function getFixtureStrategyMock()
    {
        $fixture = $this->getMockBuilder(Fixture::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();

        return $fixture;
    }

    protected function getResponseMock()
    {
        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->setMethods(['getBody'])
            ->getMock();

        return $response;
    }

    public function testRequest()
    {
        $fixtureData = 'Test fixture data';
        $fixture = $this->getFixtureStrategyMock();
        $fixture
            ->expects($this->once())
            ->method('request')
            ->will($this->returnValue($fixtureData));

        $this->fallbackFactory
            ->expects($this->once())
            ->method('create')
            ->with($this->equalTo('fixture'), $this->equalTo([$fixtureData]))
            ->will($this->returnValue($fixture));

        $response = $this->getResponseMock();
        $response
            ->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue($fixtureData));

        $this->responseFactory
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($response));

        $result = $this->client->request('get', 'http://test.com', [], ['fixture', [$fixtureData]]);

        $this->assertEquals($fixtureData, $result->getBody());
    }
}
