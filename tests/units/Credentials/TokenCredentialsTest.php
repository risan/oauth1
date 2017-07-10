<?php

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
    function token_credentials_must_be_an_instance_of_credentials_interface()
    {
        $this->assertInstanceOf(CredentialsInterface::class, $this->tokenCredentials);
    }

    /** @test */
    function token_credentials_must_be_a_subclass_of_server_issued_credentials_class()
    {
        $this->assertInstanceOf(ServerIssuedCredentials::class, $this->tokenCredentials);
    }

    /** @test */
    function token_credentials_can_get_identifier()
    {
        $this->assertEquals('foo', $this->tokenCredentials->getIdentifier());
    }

    /** @test */
    function token_credentials_can_get_secret()
    {
        $this->assertEquals('bar', $this->tokenCredentials->getSecret());
    }
}
