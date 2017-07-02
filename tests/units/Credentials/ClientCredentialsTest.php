<?php

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Credentials\ClientCredentials;

class ClientCredentialsTest extends TestCase
{
    private $clientCredentials;

    function setUp()
    {
        $this->clientCredentials = new ClientCredentials('foo', 'bar');
    }

    /** @test */
    function client_credentials_can_get_identifier()
    {
        $this->assertEquals('foo', $this->clientCredentials->getIdentifier());
    }

    /** @test */
    function client_credentials_can_get_secret()
    {
        $this->assertEquals('bar', $this->clientCredentials->getSecret());
    }
}
