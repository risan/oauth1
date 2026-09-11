<?php

declare(strict_types=1);

namespace Risan\OAuth1\Test\Unit\Credentials;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Credentials\CredentialsInterface;
use Risan\OAuth1\Credentials\ServerIssuedCredentials;
use Risan\OAuth1\Credentials\TemporaryCredentials;

class TemporaryCredentialsTest extends TestCase
{
    private $temporaryCredentials;

    protected function setUp(): void
    {
        $this->temporaryCredentials = new TemporaryCredentials('foo', 'bar');
    }

    #[Test]
    public function it_implements_credentials_interface()
    {
        $this->assertInstanceOf(CredentialsInterface::class, $this->temporaryCredentials);
    }

    #[Test]
    public function it_must_be_a_subclass_of_server_issued_credentials_class()
    {
        $this->assertInstanceOf(ServerIssuedCredentials::class, $this->temporaryCredentials);
    }

    #[Test]
    public function it_can_get_identifier()
    {
        $this->assertEquals('foo', $this->temporaryCredentials->getIdentifier());
    }

    #[Test]
    public function it_can_get_secret()
    {
        $this->assertEquals('bar', $this->temporaryCredentials->getSecret());
    }
}
