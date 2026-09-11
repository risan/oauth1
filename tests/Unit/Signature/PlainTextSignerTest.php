<?php

declare(strict_types=1);

namespace Risan\OAuth1\Test\Unit\Signature;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Credentials\ClientCredentials;
use Risan\OAuth1\Credentials\TokenCredentials;
use Risan\OAuth1\Signature\KeyBasedSignerInterface;
use Risan\OAuth1\Signature\PlainTextSigner;
use Risan\OAuth1\Signature\SignerInterface;

class PlainTextSignerTest extends TestCase
{
    private $plainTextSigner;

    private $clientCredentials;

    private $tokenCredentials;

    protected function setUp(): void
    {
        $this->plainTextSigner = new PlainTextSigner;
        $this->clientCredentials = new ClientCredentials('client_id', 'client_secret');
        $this->tokenCredentials = new TokenCredentials('token_id', 'token_secret');
    }

    #[Test]
    public function it_is_implements_signer_interface()
    {
        $this->assertInstanceOf(SignerInterface::class, $this->plainTextSigner);
    }

    #[Test]
    public function it_is_an_instance_of_key_based_signer_interface()
    {
        $this->assertInstanceOf(KeyBasedSignerInterface::class, $this->plainTextSigner);
    }

    #[Test]
    public function it_can_get_valid_method()
    {
        $this->assertEquals('PLAINTEXT', $this->plainTextSigner->getMethod());
    }

    #[Test]
    public function it_is_key_based()
    {
        $this->assertTrue($this->plainTextSigner->isKeyBased());
    }

    #[Test]
    public function it_can_set_and_get_client_credentials()
    {
        $this->assertNull($this->plainTextSigner->getClientCredentials());

        $this->assertSame($this->plainTextSigner, $this->plainTextSigner->setClientCredentials($this->clientCredentials));

        $this->assertSame($this->clientCredentials, $this->plainTextSigner->getClientCredentials());
    }

    #[Test]
    public function it_can_set_and_get_server_issued_credentials()
    {
        $this->assertNull($this->plainTextSigner->getServerIssuedCredentials());

        $this->assertSame($this->plainTextSigner, $this->plainTextSigner->setServerIssuedCredentials($this->tokenCredentials));

        $this->assertSame($this->tokenCredentials, $this->plainTextSigner->getServerIssuedCredentials());
    }

    #[Test]
    public function it_can_get_valid_key()
    {
        // No keys.
        $this->assertEquals('&', $this->plainTextSigner->getKey());

        // Client credentials only.
        $this->plainTextSigner->setClientCredentials($this->clientCredentials);
        $this->assertEquals('client_secret&', $this->plainTextSigner->getKey());

        // Client credentials and token credentials.
        $this->plainTextSigner->setServerIssuedCredentials($this->tokenCredentials);
        $this->assertEquals('client_secret&token_secret', $this->plainTextSigner->getKey());
    }

    #[Test]
    public function it_can_create_valid_signature()
    {
        // Client credentials only.
        $this->plainTextSigner->setClientCredentials($this->clientCredentials);
        $this->assertEquals('client_secret&', $this->plainTextSigner->sign('http://example.com/path', ['foo' => 'bar'], 'POST'));

        // Client credentials and token credentials.
        $this->plainTextSigner->setServerIssuedCredentials($this->tokenCredentials);
        $this->assertEquals('client_secret&token_secret', $this->plainTextSigner->sign('http://example.com/path', ['foo' => 'bar'], 'POST'));
    }
}
