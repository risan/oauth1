<?php

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Credentials\CredentialsInterface;
use Risan\OAuth1\Credentials\ServerIssuedCredentials;

class ServerIssuedCredentialsTest extends TestCase
{
    private $serverIssuedCredentialsStub;

    function setUp()
    {
        $this->serverIssuedCredentialsStub = $this->getMockForAbstractClass(ServerIssuedCredentials::class, ['foo', 'bar']);
    }

    /** @test */
    function server_issued_credentials_must_be_an_instance_of_credentials_interface()
    {
        $this->assertInstanceOf(CredentialsInterface::class, $this->serverIssuedCredentialsStub);
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
