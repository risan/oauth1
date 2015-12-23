<?php

use OAuth1Client\Upwork;
use OAuth1Client\Credentials\ClientCredentials;
use OAuth1Client\Contracts\HttpClientInterface;
use OAuth1Client\Contracts\Signatures\SignatureInterface;
use OAuth1Client\Contracts\Credentials\TemporaryCredentialsInterface;

class UpworkTest extends PHPUnit_Framework_TestCase {
    protected $client;

    protected $clientCredentials;

    protected $temporaryCredentialsUrl;

    function setUp()
    {
        $this->clientCredentials = new ClientCredentials('foo', 'bar');

        $this->client = new Upwork($this->clientCredentials);

        $this->temporaryCredentialsUrl = 'https://www.upwork.com/api/auth/v1/oauth/token/request';
    }

    /** @test */
    function upwork_has_http_client()
    {
        $this->assertInstanceOf(HttpClientInterface::class, $this->client->httpClient());
    }

    /** @test */
    function upwork_has_client_credentials()
    {
        $this->assertInstanceOf(ClientCredentials::class, $this->client->clientCredentials());
    }

    /** @test */
    function upwork_has_signature()
    {
        $this->assertInstanceOf(SignatureInterface::class, $this->client->signature());
    }

    /** @test */
    function upwork_can_generate_nonce()
    {
        $nonce = $this->client->nonce();

        $this->assertTrue(is_string($nonce));

        $this->assertEquals(32, strlen($nonce));
    }

    /** @test */
    function upwork_can_get_timestamp()
    {
        $timestamp = $this->client->timestamp();

        $this->assertLessThanOrEqual(time(), $timestamp);

        $this->assertGreaterThan(strtotime('-1 minute'), $timestamp);
    }

    /** @test */
    function upwork_has_version()
    {
        $this->assertEquals('1.0', $this->client->version());
    }

    /** @test */
    function upwork_has_temporary_credentials_url()
    {
        return $this->assertEquals($this->temporaryCredentialsUrl, $this->client->temporaryCredentialsUrl());
    }

    /** @test */
    function upwork_can_create_temporary_credentials()
    {
        $this->assertInstanceOf(TemporaryCredentialsInterface::class, $this->client->temporaryCredentials());
    }
}
