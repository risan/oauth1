<?php

use OAuth1\Tokens\AccessToken;
use GuzzleHttp\Client as HttpClient;

class AccessTokenTest extends PHPUnit_Framework_TestCase {
    protected $accessToken;
    protected $httpClient;

    function setUp()
    {
        $this->accessToken = new AccessToken('foo', 'bar');

        $this->httpClient = new HttpClient();
    }

    /** @test */
    function access_token_has_key()
    {
        $this->assertEquals('foo', $this->accessToken->key());
    }

    /** @test */
    function access_token_has_secret()
    {
        $this->assertEquals('bar', $this->accessToken->secret());
    }

    /** @test */
    function access_token_can_be_created_from_http_response()
    {
        $response = $this->httpClient->get('http://www.mocky.io/v2/567a64390f0000eb051aef7c');

        $accessToken = AccessToken::fromHttpResponse($response);

        $this->assertInstanceOf(AccessToken::class, $accessToken);

        $this->assertEquals('foo', $accessToken->key());

        $this->assertEquals('bar', $accessToken->secret());
    }
}
