<?php

use OAuth1Client\Credentials\ClientCredentials;

class ClientCredentialsTest extends PHPUnit_Framework_TestCase {
    protected $clientCredentials;

    function setUp()
    {
        $this->clientCredentials = new ClientCredentials('foo', 'bar', 'http://baz.qux');
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
    function client_credentials_has_callback_uri()
    {
        $this->assertEquals('http://baz.qux', $this->clientCredentials->callbackUri());
    }
}
