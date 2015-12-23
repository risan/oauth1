<?php

use OAuth1Client\Upwork;
use OAuth1Client\OAuth1Client;
use OAuth1Client\Contracts\UpworkInterface;
use OAuth1Client\Credentials\ClientCredentials;
use OAuth1Client\Contracts\HttpClientInterface;
use OAuth1Client\Credentials\TemporaryCredentials;
use OAuth1Client\Contracts\Signatures\SignatureInterface;
use OAuth1Client\Contracts\Credentials\TemporaryCredentialsInterface;

class UpworkTest extends PHPUnit_Framework_TestCase {
    protected $client;

    protected $clientCredentials;

    protected $temporaryCredentials;

    protected $temporaryCredentialsUrl;

    protected $tokenCredentialsUrl;

    protected $authorizationUrl;

    function setUp()
    {
        $this->clientCredentials = new ClientCredentials('foo', 'bar');

        $this->temporaryCredentials = new TemporaryCredentials('baz', 'qux');

        $this->client = new Upwork($this->clientCredentials);

        $this->temporaryCredentialsUrl = 'https://www.upwork.com/api/auth/v1/oauth/token/request';

        $this->authorizationUrl = 'https://www.upwork.com/services/api/auth';

        $this->tokenCredentialsUrl = 'https://www.upwork.com/api/auth/v1/oauth/token/access';
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
    function upwork_has_base_protocol_parameters()
    {
        $parameters = $this->client->baseProtocolParameters();

        $this->assertArrayHasKey('oauth_consumer_key', $parameters);
        $this->assertArrayHasKey('oauth_nonce', $parameters);
        $this->assertArrayHasKey('oauth_signature_method', $parameters);
        $this->assertArrayHasKey('oauth_timestamp', $parameters);
        $this->assertArrayHasKey('oauth_version', $parameters);
    }

    /** @test */
    function upwork_can_generate_authorization_headers()
    {
        $parameters = ['foo' => 'bar'];

        $this->assertEquals('OAuth foo=bar', $this->client->authorizationHeaders($parameters));
    }

    /** @test */
    function upwork_can_generate_temporary_credentials_headers()
    {
        $this->assertArrayHasKey('Authorization', $this->client->temporaryCredentialsHeaders());
    }

    /** @test */
    function upwork_can_create_temporary_credentials()
    {
        $upwork = new UpworkMock($this->clientCredentials);

        $this->assertInstanceOf(TemporaryCredentialsInterface::class, $upwork->temporaryCredentials());
    }

    /** @test */
    function upwork_has_authorization_url()
    {
        return $this->assertEquals($this->authorizationUrl, $this->client->authorizationUrl());
    }

    /** @test */
    function upwork_can_build_authorization_url()
    {
        $expected = $this->authorizationUrl . "?oauth_token=baz";

        return $this->assertEquals($expected, $this->client->buildAuthorizationUrl($this->temporaryCredentials));
    }

    /** @test */
    function upwork_has_token_credentials_url()
    {
        return $this->assertEquals($this->tokenCredentialsUrl, $this->client->tokenCredentialsUrl());
    }
}

class UpworkMock extends OAuth1Client implements UpworkInterface {
    /**
     * Temporary credentials url.
     *
     * @return string
     */
    public function temporaryCredentialsUrl()
    {
        return 'http://www.mocky.io/v2/567a64390f0000eb051aef7c';
    }

    /**
     * Authorization url.
     *
     * @return string
     */
    public function authorizationUrl()
    {
        return 'http://www.mocky.io/v2/567a64390f0000eb051aef7c';
    }

    /**
     * Access token credentials url.
     *
     * @return string
     */
    public function tokenCredentialsUrl()
    {
        return 'http://www.mocky.io/v2/567a64390f0000eb051aef7c';
    }
}
