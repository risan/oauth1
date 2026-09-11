<?php

declare(strict_types=1);

namespace Risan\OAuth1\Test\Unit\Credentials;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Credentials\ClientCredentials;
use Risan\OAuth1\Credentials\CredentialsInterface;

class ClientCredentialsTest extends TestCase
{
    private $clientCredentials;

    protected function setUp(): void
    {
        $this->clientCredentials = new ClientCredentials('foo', 'bar');
    }

    #[Test]
    public function it_must_be_an_instance_of_credentials_interface()
    {
        $this->assertInstanceOf(CredentialsInterface::class, $this->clientCredentials);
    }

    #[Test]
    public function it_can_get_identifier()
    {
        $this->assertEquals('foo', $this->clientCredentials->getIdentifier());
    }

    #[Test]
    public function it_can_get_secret()
    {
        $this->assertEquals('bar', $this->clientCredentials->getSecret());
    }
}
