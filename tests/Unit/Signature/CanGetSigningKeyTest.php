<?php

declare(strict_types=1);

namespace Risan\OAuth1\Test\Unit\Signature;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Credentials\ClientCredentials;
use Risan\OAuth1\Credentials\TokenCredentials;
use Risan\OAuth1\Signature\CanGetSigningKey;

class CanGetSigningKeyTest extends TestCase
{
    private $canGetSigningKeyStub;

    private $clientCredentials;

    private $tokenCredentials;

    protected function setUp(): void
    {
        $this->canGetSigningKeyStub = new class
        {
            use CanGetSigningKey;
        };
        $this->clientCredentials = new ClientCredentials('client_id', 'client_secret');
        $this->tokenCredentials = new TokenCredentials('token_id', 'token_secret');
    }

    #[Test]
    public function it_is_key_based()
    {
        $this->assertTrue($this->canGetSigningKeyStub->isKeyBased());
    }

    #[Test]
    public function it_can_set_and_get_client_credentials()
    {
        $this->assertNull($this->canGetSigningKeyStub->getClientCredentials());

        $this->assertSame($this->canGetSigningKeyStub, $this->canGetSigningKeyStub->setClientCredentials($this->clientCredentials));

        $this->assertSame($this->clientCredentials, $this->canGetSigningKeyStub->getClientCredentials());
    }

    #[Test]
    public function it_can_set_and_get_server_issued_credentials()
    {
        $this->assertNull($this->canGetSigningKeyStub->getServerIssuedCredentials());

        $this->assertSame($this->canGetSigningKeyStub, $this->canGetSigningKeyStub->setServerIssuedCredentials($this->tokenCredentials));

        $this->assertSame($this->tokenCredentials, $this->canGetSigningKeyStub->getServerIssuedCredentials());
    }

    #[Test]
    public function it_can_get_valid_key()
    {
        // Without any key.
        $this->assertEquals('&', $this->canGetSigningKeyStub->getKey());

        // Client credentials only.
        $this->canGetSigningKeyStub->setClientCredentials($this->clientCredentials);
        $this->assertEquals('client_secret&', $this->canGetSigningKeyStub->getKey());

        // Client credentials and token credentials.
        $this->canGetSigningKeyStub->setServerIssuedCredentials($this->tokenCredentials);
        $this->assertEquals('client_secret&token_secret', $this->canGetSigningKeyStub->getKey());
    }
}
