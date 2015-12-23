<?php

use GuzzleHttp\Client as HttpClient;
use OAuth1Client\Credentials\TemporaryCredentials;

class TemporaryCredentialsTest extends PHPUnit_Framework_TestCase {
    protected $temporaryCredentials;

    function setUp()
    {
        $this->temporaryCredentials = new TemporaryCredentials('foo', 'bar');

        $this->httpClient = new HttpClient();
    }

    /** @test */
    function temporary_credentials_has_identifier()
    {
        $this->assertEquals('foo', $this->temporaryCredentials->identifier());
    }

    /** @test */
    function temporary_credentials_has_secret()
    {
        $this->assertEquals('bar', $this->temporaryCredentials->secret());
    }

    /** @test */
    function temporary_credentials_can_be_instantiated_from_http_response()
    {
        $response = $this->httpClient->get('http://www.mocky.io/v2/567a64390f0000eb051aef7c');

        $temporaryCredentials = TemporaryCredentials::fromHttpResponse($response);

        $this->assertInstanceOf(TemporaryCredentials::class, $temporaryCredentials);

        $this->assertEquals('foo', $temporaryCredentials->identifier());

        $this->assertEquals('bar', $temporaryCredentials->secret());
    }

    /** @test */
    function temporary_credentials_not_exposing_secret_property_when_casting_to_string()
    {
        $this->assertEquals('foo', (string) $this->temporaryCredentials);
    }
}
