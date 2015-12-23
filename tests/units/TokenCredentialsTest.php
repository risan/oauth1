<?php

use GuzzleHttp\Client as HttpClient;
use OAuth1Client\Credentials\TokenCredentials;

class TokenCredentialsTest extends PHPUnit_Framework_TestCase {
    protected $tokenCredentials;

    protected $httpClient;

    function setUp()
    {
        $this->tokenCredentials = new TokenCredentials('foo', 'bar');

        $this->httpClient = new HttpClient();
    }

    /** @test */
    function token_credentials_has_identifier()
    {
        $this->assertEquals('foo', $this->tokenCredentials->identifier());
    }

    /** @test */
    function token_credentials_has_secret()
    {
        $this->assertEquals('bar', $this->tokenCredentials->secret());
    }

    /** @test */
    function token_credentials_can_be_instantiated_from_http_response()
    {
        $response = $this->httpClient->get('http://www.mocky.io/v2/567a64390f0000eb051aef7c');

        $tokenCredentials = TokenCredentials::fromHttpResponse($response);

        $this->assertInstanceOf(TokenCredentials::class, $tokenCredentials);

        $this->assertEquals('foo', $tokenCredentials->identifier());

        $this->assertEquals('bar', $tokenCredentials->secret());
    }

    /** @test */
    function token_credentials_not_exposing_secret_property_when_casting_to_string()
    {
        $this->assertEquals('foo', (string) $this->tokenCredentials);
    }
}
