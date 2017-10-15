<?php

namespace Risan\OAuth1\Test\Unit\Credentials;

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
    function it_implements_credentials_interface()
    {
        $this->assertInstanceOf(CredentialsInterface::class, $this->serverIssuedCredentialsStub);
    }

    /** @test */
    function it_can_get_identifier()
    {
        $this->assertEquals('foo', $this->serverIssuedCredentialsStub->getIdentifier());
    }

    /** @test */
    function it_can_get_secret()
    {
        $this->assertEquals('bar', $this->serverIssuedCredentialsStub->getSecret());
    }
}
