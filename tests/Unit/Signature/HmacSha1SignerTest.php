<?php

namespace Risan\OAuth1\Test\Unit\Signature;

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Signature\HmacSha1Signer;
use Risan\OAuth1\Signature\SignerInterface;
use Risan\OAuth1\Credentials\TokenCredentials;
use Risan\OAuth1\Credentials\ClientCredentials;
use Risan\OAuth1\Signature\KeyBasedSignerInterface;
use Risan\OAuth1\Signature\BaseStringSignerInterface;

class HmacSha1SignerTest extends TestCase
{
    private $hmacSha1Signer;
    private $clientCredentials;
    private $tokenCredentials;

    function setUp()
    {
        $this->hmacSha1Signer = new HmacSha1Signer;
        $this->clientCredentials = new ClientCredentials('client_id', 'client_secret');
        $this->tokenCredentials = new TokenCredentials('token_id', 'token_secret');
    }

    /** @test */
    function it_is_implements_signer_interface()
    {
        $this->assertInstanceOf(SignerInterface::class, $this->hmacSha1Signer);
    }

    /** @test */
    function it_is_an_instance_of_base_string_signer_interface()
    {
        $this->assertInstanceOf(BaseStringSignerInterface::class, $this->hmacSha1Signer);
    }

    /** @test */
    function it_is_an_instance_of_key_based_signer_interface()
    {
        $this->assertInstanceOf(KeyBasedSignerInterface::class, $this->hmacSha1Signer);
    }

    /** @test */
    function it_can_get_valid_method()
    {
        $this->assertEquals('HMAC-SHA1', $this->hmacSha1Signer->getMethod());
    }

     /** @test */
    function it_is_key_based()
    {
        $this->assertTrue($this->hmacSha1Signer->isKeyBased());
    }

    /** @test */
    function it_can_build_base_string()
    {
        $baseString = $this->hmacSha1Signer->buildBaseString('http://example.com/path', ['foo' => 'bar'], 'POST');

        $this->assertEquals('POST&http%3A%2F%2Fexample.com%2Fpath&foo%3Dbar', $baseString);
    }

    /** @test */
    function it_can_set_and_get_client_credentials()
    {
        $this->assertNull($this->hmacSha1Signer->getClientCredentials());

        $this->assertSame($this->hmacSha1Signer, $this->hmacSha1Signer->setClientCredentials($this->clientCredentials));

        $this->assertSame($this->clientCredentials, $this->hmacSha1Signer->getClientCredentials());
    }

    /** @test */
    function it_can_set_and_get_server_issued_credentials()
    {
        $this->assertNull($this->hmacSha1Signer->getServerIssuedCredentials());

        $this->assertSame($this->hmacSha1Signer, $this->hmacSha1Signer->setServerIssuedCredentials($this->tokenCredentials));

        $this->assertSame($this->tokenCredentials, $this->hmacSha1Signer->getServerIssuedCredentials());
    }

    /** @test */
    function it_can_get_valid_key()
    {
        // No keys.
        $this->assertEquals('&', $this->hmacSha1Signer->getKey());

        // Client credentials only.
        $this->hmacSha1Signer->setClientCredentials($this->clientCredentials);
        $this->assertEquals('client_secret&', $this->hmacSha1Signer->getKey());

        // Client credentials and token credentials.
        $this->hmacSha1Signer->setServerIssuedCredentials($this->tokenCredentials);
        $this->assertEquals('client_secret&token_secret', $this->hmacSha1Signer->getKey());
    }

    /** @test */
    function it_can_hash_data()
    {
        // Client credentials only.
        $this->hmacSha1Signer->setClientCredentials($this->clientCredentials);
        $this->assertEquals(hex2bin('2db1ee1dfcfe5ac52ee83d8e23fd5f88cce5e97e'), $this->hmacSha1Signer->hash('foo'));

        // Client credentials and token credentials.
        $this->hmacSha1Signer->setServerIssuedCredentials($this->tokenCredentials);
        $this->assertEquals(hex2bin('539643cfc14d1027b9ffd31537988ec0e63518e2'), $this->hmacSha1Signer->hash('foo'));
    }

    /** @test */
    function it_can_create_valid_signature()
    {
        // Client credentials only.
        $this->hmacSha1Signer->setClientCredentials($this->clientCredentials);
        $this->assertEquals('xQN8FRuwOGeo3oxtqfc3Fr81UJc=', $this->hmacSha1Signer->sign('http://example.com/path', ['foo' => 'bar'], 'POST'));

        // Client credentials and token credentials.
        $this->hmacSha1Signer->setServerIssuedCredentials($this->tokenCredentials);
        $this->assertEquals('KzNAACBmOL47y2WpvUWnnzAx7Sg=', $this->hmacSha1Signer->sign('http://example.com/path', ['foo' => 'bar'], 'POST'));
    }
}
