<?php

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Credentials\TemporaryCredentials;

class TemporaryCredentialsTest extends TestCase
{
    private $temporaryCredentials;

    function setUp()
    {
        $this->temporaryCredentials = new TemporaryCredentials('foo', 'bar');
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
