<?php

namespace Risan\OAuth1\Test\Unit;

use Risan\OAuth1\HttpClient;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client as Guzzle;
use Risan\OAuth1\HttpClientInterface;
use Psr\Http\Message\ResponseInterface;

class HttpClientTest extends TestCase
{
    private $guzzleStub;
    private $responseStub;
    private $httpClient;

    function setUp()
    {
        $this->guzzleStub = $this->createMock(Guzzle::class);
        $this->responseStub = $this->createMock(Response::class);
        $this->httpClient = new HttpClient($this->guzzleStub);
    }

    /** @test */
    function http_client_is_an_instance_of_http_client_interface()
    {
        $this->assertInstanceOf(HttpClientInterface::class, $this->httpClient);
    }

    /** @test */
    function http_client_can_get_guzzle_instance()
    {
        $this->assertInstanceOf(Guzzle::class, $this->httpClient->getGuzzle());
    }

    /** @test */
    function http_client_can_create_and_send_http_request()
    {
        $this->guzzleStub->expects($this->once())
            ->method('request')
            ->with('POST', 'http://example.com', ['foo' => 'bar'])
            ->willReturn($this->responseStub);

        $this->assertInstanceOf(
            ResponseInterface::class,
            $this->httpClient->request('POST', 'http://example.com', ['foo' => 'bar'])
        );
    }

    /** @test */
    function http_client_can_create_and_send_http_post_request()
    {
        $this->guzzleStub->expects($this->once())
            ->method('request')
            ->with('POST', 'http://example.com', ['foo' => 'bar'])
            ->willReturn($this->responseStub);

        $this->assertInstanceOf(
            ResponseInterface::class,
            $this->httpClient->post('http://example.com', ['foo' => 'bar'])
        );
    }
}
