<?php

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Credentials\Credentials;

class CredentialsTest extends TestCase
{
    private $credentialsStub;

    function setUp()
    {
        $this->credentialsStub = $this->getMockForAbstractClass(Credentials::class, ['foo', 'bar']);
    }

    /** @test */
    function credentials_can_get_identifier()
    {
        $this->assertEquals('foo', $this->credentialsStub->getIdentifier());
    }

    /** @test */
    function credentials_can_get_secret()
    {
        $this->assertEquals('bar', $this->credentialsStub->getSecret());
    }
}
