<?php

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
    function temporary_credentials_must_be_an_instance_of_credentials_interface()
    {
        $this->assertInstanceOf(CredentialsInterface::class, $this->temporaryCredentials);
    }

    /** @test */
    function temporary_credentials_must_be_a_subclass_of_server_issued_credentials_class()
    {
        $this->assertInstanceOf(ServerIssuedCredentials::class, $this->temporaryCredentials);
    }

    /** @test */
    function temporary_credentials_can_get_identifier()
    {
        $this->assertEquals('foo', $this->temporaryCredentials->getIdentifier());
    }

    /** @test */
    function temporary_credentials_can_get_secret()
    {
        $this->assertEquals('bar', $this->temporaryCredentials->getSecret());
    }
}
