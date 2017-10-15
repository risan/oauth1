<?php

namespace Risan\OAuth1\Test\Unit\Credentials;

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Credentials\CredentialsInterface;
use Risan\OAuth1\Credentials\TemporaryCredentials;
use Risan\OAuth1\Credentials\ServerIssuedCredentials;

class TemporaryCredentialsTest extends TestCase
{
    private $temporaryCredentials;

    function setUp()
    {
        $this->temporaryCredentials = new TemporaryCredentials('foo', 'bar');
    }

    /** @test */
    function it_implements_credentials_interface()
    {
        $this->assertInstanceOf(CredentialsInterface::class, $this->temporaryCredentials);
    }

    /** @test */
    function it_must_be_a_subclass_of_server_issued_credentials_class()
    {
        $this->assertInstanceOf(ServerIssuedCredentials::class, $this->temporaryCredentials);
    }

    /** @test */
    function it_can_get_identifier()
    {
        $this->assertEquals('foo', $this->temporaryCredentials->getIdentifier());
    }

    /** @test */
    function it_can_get_secret()
    {
        $this->assertEquals('bar', $this->temporaryCredentials->getSecret());
    }
}
