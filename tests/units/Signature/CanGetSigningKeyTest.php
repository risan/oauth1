<?php

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Signature\CanGetSigningKey;
use Risan\OAuth1\Credentials\TokenCredentials;
use Risan\OAuth1\Credentials\ClientCredentials;

class CanGetSigningKeyTest extends TestCase
{
    private $canGetSigningKeyStub;
    private $clientCredentials;

    function setUp()
    {
        $this->canGetSigningKeyStub = $this->getMockForTrait(CanGetSigningKey::class);
        $this->clientCredentials = new ClientCredentials('client_id', 'client_secret');
        $this->tokenCredentials = new TokenCredentials('token_id', 'token_secret');
    }

    /** @test */
    function can_get_signing_key_trait_can_set_and_get_client_credentials()
    {
        $this->assertNull($this->canGetSigningKeyStub->getClientCredentials());

        $this->assertSame($this->canGetSigningKeyStub, $this->canGetSigningKeyStub->setClientCredentials($this->clientCredentials));

        $this->assertSame($this->clientCredentials, $this->canGetSigningKeyStub->getClientCredentials());
    }

    /** @test */
    function can_get_signing_key_trait_can_set_and_get_server_issued_credentials()
    {
        $this->assertNull($this->canGetSigningKeyStub->getServerIssuedCredentials());

        $this->assertSame($this->canGetSigningKeyStub, $this->canGetSigningKeyStub->setServerIssuedCredentials($this->tokenCredentials));

        $this->assertSame($this->tokenCredentials, $this->canGetSigningKeyStub->getServerIssuedCredentials());
    }

    /** @test */
    function can_get_signing_key_trait_can_get_valid_key()
    {
        // Client credentials only.
        $this->canGetSigningKeyStub->setClientCredentials($this->clientCredentials);
        $this->assertEquals('client_secret&', $this->canGetSigningKeyStub->getKey());

        // Client credentials and token credentials.
        $this->canGetSigningKeyStub->setServerIssuedCredentials($this->tokenCredentials);
        $this->assertEquals('client_secret&token_secret', $this->canGetSigningKeyStub->getKey());
    }
}
