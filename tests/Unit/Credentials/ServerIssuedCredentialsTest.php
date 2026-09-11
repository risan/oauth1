<?php

declare(strict_types=1);

namespace Risan\OAuth1\Test\Unit\Credentials;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Credentials\CredentialsInterface;
use Risan\OAuth1\Credentials\ServerIssuedCredentials;

class ServerIssuedCredentialsTest extends TestCase
{
    private $serverIssuedCredentialsStub;

    protected function setUp(): void
    {
        $this->serverIssuedCredentialsStub = new class('foo', 'bar') extends ServerIssuedCredentials {
        };
    }

    #[Test]
    public function it_implements_credentials_interface()
    {
        $this->assertInstanceOf(CredentialsInterface::class, $this->serverIssuedCredentialsStub);
    }

    #[Test]
    public function it_can_get_identifier()
    {
        $this->assertEquals('foo', $this->serverIssuedCredentialsStub->getIdentifier());
    }

    #[Test]
    public function it_can_get_secret()
    {
        $this->assertEquals('bar', $this->serverIssuedCredentialsStub->getSecret());
    }
}
