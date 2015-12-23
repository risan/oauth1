<?php

use OAuth1Client\Credentials\ClientCredentials;

class ClientCredentialsTest extends PHPUnit_Framework_TestCase {
    protected $clientCredentials;

    function setUp()
    {
        $this->clientCredentials = new ClientCredentials('foo', 'bar');
    }

    /** @test */
    function client_credentials_has_identifier()
    {
        $this->assertEquals('foo', $this->clientCredentials->identifier());
    }

    /** @test */
    function client_credentials_has_secret()
    {
        $this->assertEquals('bar', $this->clientCredentials->secret());
    }

    /** @test */
    function client_credentials_not_exposing_secret_property_when_casting_to_string()
    {
        $this->assertEquals('foo', (string) $this->clientCredentials);
    }
}
