<?php

namespace Risan\OAuth1\Test\Unit\Credentials;

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Credentials\ClientCredentials;
use Risan\OAuth1\Credentials\CredentialsInterface;

class ClientCredentialsTest extends TestCase
{
    private $clientCredentials;

    function setUp()
    {
        $this->clientCredentials = new ClientCredentials('foo', 'bar');
    }

    /** @test */
    function it_must_be_an_instance_of_credentials_interface()
    {
        $this->assertInstanceOf(CredentialsInterface::class, $this->clientCredentials);
    }

    /** @test */
    function it_can_get_identifier()
    {
        $this->assertEquals('foo', $this->clientCredentials->getIdentifier());
    }

    /** @test */
    function it_can_get_secret()
    {
        $this->assertEquals('bar', $this->clientCredentials->getSecret());
    }
}
