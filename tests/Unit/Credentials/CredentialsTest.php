<?php

namespace Risan\OAuth1\Test\Unit\Credentials;

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Credentials\Credentials;
use Risan\OAuth1\Credentials\CredentialsInterface;

class CredentialsTest extends TestCase
{
    private $credentialsStub;

    function setUp()
    {
        $this->credentialsStub = $this->getMockForAbstractClass(Credentials::class, ['foo', 'bar']);
    }

    /** @test */
    function credentials_must_be_an_instance_of_credentials_interface()
    {
        $this->assertInstanceOf(CredentialsInterface::class, $this->credentialsStub);
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
