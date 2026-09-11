<?php

declare(strict_types=1);

namespace Risan\OAuth1\Test\Unit\Credentials;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Credentials\CredentialsInterface;
use Risan\OAuth1\Credentials\ServerIssuedCredentials;
use Risan\OAuth1\Credentials\TokenCredentials;

class TokenCredentialsTest extends TestCase
{
    private $tokenCredentials;

    protected function setUp(): void
    {
        $this->tokenCredentials = new TokenCredentials('foo', 'bar');
    }

    #[Test]
    public function it_implements_credentials_interface()
    {
        $this->assertInstanceOf(CredentialsInterface::class, $this->tokenCredentials);
    }

    #[Test]
    public function it_must_be_a_subclass_of_server_issued_credentials_class()
    {
        $this->assertInstanceOf(ServerIssuedCredentials::class, $this->tokenCredentials);
    }

    #[Test]
    public function it_can_get_identifier()
    {
        $this->assertEquals('foo', $this->tokenCredentials->getIdentifier());
    }

    #[Test]
    public function it_can_get_secret()
    {
        $this->assertEquals('bar', $this->tokenCredentials->getSecret());
    }
}
