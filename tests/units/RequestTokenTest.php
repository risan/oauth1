<?php

use OAuth1\Tokens\RequestToken;
use GuzzleHttp\Client as HttpClient;

class RequestTokenTest extends PHPUnit_Framework_TestCase {
    protected $requestToken;
    protected $httpClient;

    function setUp()
    {
        $this->requestToken = new RequestToken('foo', 'bar');

        $this->httpClient = new HttpClient();
    }

    /** @test */
    function request_token_has_key()
    {
        $this->assertEquals('foo', $this->requestToken->key());
    }

    /** @test */
    function request_token_has_secret()
    {
        $this->assertEquals('bar', $this->requestToken->secret());
    }

    /** @test */
    function request_token_can_be_created_from_http_response()
    {
        $response = $this->httpClient->get('http://www.mocky.io/v2/567a64390f0000eb051aef7c');

        $requestToken = RequestToken::fromHttpResponse($response);

        $this->assertInstanceOf(RequestToken::class, $requestToken);

        $this->assertEquals('foo', $requestToken->key());

        $this->assertEquals('bar', $requestToken->secret());
    }
}
