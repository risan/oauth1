<?php

declare(strict_types=1);

namespace Risan\OAuth1\Test\Unit\Credentials;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Credentials\Credentials;
use Risan\OAuth1\Credentials\CredentialsInterface;

class CredentialsTest extends TestCase
{
    private $credentialsStub;

    protected function setUp(): void
    {
        $this->credentialsStub = new class('foo', 'bar') extends Credentials {
        };
    }

    #[Test]
    public function it_implements_credentials_interface()
    {
        $this->assertInstanceOf(CredentialsInterface::class, $this->credentialsStub);
    }

    #[Test]
    public function it_can_get_identifier()
    {
        $this->assertEquals('foo', $this->credentialsStub->getIdentifier());
    }

    #[Test]
    public function it_can_get_secret()
    {
        $this->assertEquals('bar', $this->credentialsStub->getSecret());
    }
}
