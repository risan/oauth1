<?php

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Credentials\ServerIssuedCredentials;

class ServerIssuedCredentialsTest extends TestCase
{
    private $serverIssuedCredentialsStub;

    function setUp()
    {
        $this->serverIssuedCredentialsStub = $this->getMockForAbstractClass(ServerIssuedCredentials::class, ['foo', 'bar']);
    }

    /** @test */
    function server_issued_credentials_can_get_identifier()
    {
        $this->assertEquals('foo', $this->serverIssuedCredentialsStub->getIdentifier());
    }

    /** @test */
    function server_issued_credentials_can_get_secret()
    {
        $this->assertEquals('bar', $this->serverIssuedCredentialsStub->getSecret());
    }
}
