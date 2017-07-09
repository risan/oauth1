<?php

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Credentials\TokenCredentials;

class TokenCredentialsTest extends TestCase
{
    private $tokenCredentials;

    function setUp()
    {
        $this->tokenCredentials = new TokenCredentials('foo', 'bar');
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
