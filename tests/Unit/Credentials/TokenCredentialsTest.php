<?php

namespace Risan\OAuth1\Test\Unit\Credentials;

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Credentials\TokenCredentials;
use Risan\OAuth1\Credentials\CredentialsInterface;
use Risan\OAuth1\Credentials\ServerIssuedCredentials;

class TokenCredentialsTest extends TestCase
{
    private $tokenCredentials;

    function setUp()
    {
        $this->tokenCredentials = new TokenCredentials('foo', 'bar');
    }

    /** @test */
    function it_implements_credentials_interface()
    {
        $this->assertInstanceOf(CredentialsInterface::class, $this->tokenCredentials);
    }

    /** @test */
    function it_must_be_a_subclass_of_server_issued_credentials_class()
    {
        $this->assertInstanceOf(ServerIssuedCredentials::class, $this->tokenCredentials);
    }

    /** @test */
    function it_can_get_identifier()
    {
        $this->assertEquals('foo', $this->tokenCredentials->getIdentifier());
    }

    /** @test */
    function it_can_get_secret()
    {
        $this->assertEquals('bar', $this->tokenCredentials->getSecret());
    }
}
