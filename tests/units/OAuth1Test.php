<?php

use OAuth1\OAuth1;
use OAuth1\Tokens\RequestToken;
use OAuth1\Contracts\ConfigInterface;
use OAuth1\Contracts\HttpClientInterface;
use OAuth1\Contracts\Signers\SignerInterface;
use OAuth1\Contracts\Tokens\AccessTokenInterface;
use OAuth1\Contracts\Tokens\RequestTokenInterface;

class OAuth1Test extends PHPUnit_Framework_TestCase {
    protected $config;
    protected $oauth1;
    protected $requestToken;

    function setUp()
    {
        $this->config = [
            'consumer_key' => 'key',
            'consumer_secret' => 'secret',
            'callback_url' => 'http://callback.foo',
            'request_token_url' => 'http://www.mocky.io/v2/567a64390f0000eb051aef7c',
            'authorization_url' => 'http://authorization.foo',
            'access_token_url' => 'http://www.mocky.io/v2/567a64390f0000eb051aef7c'
        ];

        $this->oauth1 = new OAuth1($this->config);

        $this->requestToken = new RequestToken('foo', 'bar');
    }

    /** @test */
    function oauth1_has_http_client()
    {
        $this->assertInstanceOf(HttpClientInterface::class, $this->oauth1->httpClient());
    }

    /** @test */
    function oauth1_has_config()
    {
        $this->assertInstanceOf(ConfigInterface::class, $this->oauth1->config());
    }

    /** @test */
    function oauth1_has_signer()
    {
        $this->assertInstanceOf(SignerInterface::class, $this->oauth1->signer());
    }

    /** @test */
    function uoauth1_can_generate_nonce()
    {
        $nonce = $this->oauth1->nonce();

        $this->assertTrue(is_string($nonce));

        $this->assertEquals(32, strlen($nonce));
    }

    /** @test */
    function oauth1_can_get_timestamp()
    {
        $timestamp = $this->oauth1->timestamp();

        $this->assertLessThanOrEqual(time(), $timestamp);

        $this->assertGreaterThan(strtotime('-1 minute'), $timestamp);
    }

    /** @test */
    function oauth1_has_version()
    {
        $this->assertEquals('1.0', $this->oauth1->version());
    }

    /** @test */
    function oauth1_has_base_protocol_parameters()
    {
        $parameters = $this->oauth1->baseProtocolParameters();

        $this->assertArrayHasKey('oauth_consumer_key', $parameters);
        $this->assertArrayHasKey('oauth_nonce', $parameters);
        $this->assertArrayHasKey('oauth_signature_method', $parameters);
        $this->assertArrayHasKey('oauth_timestamp', $parameters);
        $this->assertArrayHasKey('oauth_version', $parameters);
    }

    /** @test */
    function oauth1_can_generate_authorization_headers()
    {
        $parameters = ['foo' => 'bar'];

        $this->assertEquals('OAuth foo=bar', $this->oauth1->authorizationHeaders($parameters));
    }

    /** @test */
    function oauth1_has_request_token_url()
    {
        return $this->assertEquals($this->config['request_token_url'], $this->oauth1->requestTokenUrl());
    }

    /** @test */
    function oauth1_can_generate_request_token_headers()
    {
        $this->assertArrayHasKey('Authorization', $this->oauth1->requestTokenHeaders());
    }

    /** @test */
    function oauth1_can_create_request_token()
    {
        $this->assertInstanceOf(RequestTokenInterface::class, $this->oauth1->requestToken());
    }

    /** @test */
    function oauth1_has_authorization_url()
    {
        return $this->assertEquals($this->config['authorization_url'], $this->oauth1->authorizationUrl());
    }

    /** @test */
    function oauth1_can_build_authorization_url()
    {
        $expected = $this->config['authorization_url'] . '?oauth_token=' . $this->requestToken->key();

        return $this->assertEquals($expected, $this->oauth1->buildAuthorizationUrl($this->requestToken));
    }

    /** @test */
    function oauth1_has_access_token_url()
    {
        return $this->assertEquals($this->config['access_token_url'], $this->oauth1->accessTokenUrl());
    }

    /** @test */
    function oauth1_can_can_validate_request_token()
    {
        $this->assertTrue($this->oauth1->isValidToken($this->requestToken, 'foo'));
        $this->assertFalse($this->oauth1->isValidToken($this->requestToken, 'bar'));
    }

    /** @test */
    function oauth1_can_generate_access_token_headers()
    {
        $this->assertArrayHasKey('Authorization', $this->oauth1->accessTokenHeaders($this->requestToken, 'bar'));
    }

     /** @test */
    function oauth1_can_create_access_token()
    {
        $this->assertInstanceOf(AccessTokenInterface::class, $this->oauth1->accessToken($this->requestToken, 'foo', 'bar'));
    }
}
