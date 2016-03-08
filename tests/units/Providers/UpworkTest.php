<?php

use OAuth1\Providers\Upwork;
use OAuth1\Contracts\ConfigInterface;
use OAuth1\Contracts\HttpClientInterface;
use OAuth1\Contracts\Signers\HmacSha1SignerInterface;

class UpworkTest extends PHPUnit_Framework_TestCase {
    protected $config;
    protected $upwork;

    function setUp()
    {
        $this->config = [
            'consumer_key' => 'key',
            'consumer_secret' => 'secret',
            'callback_url' => 'http://callback.foo'
        ];

        $this->upwork = new Upwork($this->config);
    }

    /** @test */
    function upwork_has_default_config()
    {
        $this->assertCount(4, $this->upwork->defaultConfig());
    }

    /** @test */
    function upwork_has_http_client()
    {
        $this->assertInstanceOf(HttpClientInterface::class, $this->upwork->httpClient());
    }

    /** @test */
    function upwork_has_config()
    {
        $this->assertInstanceOf(ConfigInterface::class, $this->upwork->config());
    }

    /** @test */
    function upwork_has_hmac_sha1_signer()
    {
        $this->assertInstanceOf(HmacSha1SignerInterface::class, $this->upwork->signer());
    }

    /** @test */
    function upwork_has_valid_request_token_url()
    {
        $this->assertEquals('https://www.upwork.com/api/auth/v1/oauth/token/request', $this->upwork->config()->requestTokenUrl());
    }

    /** @test */
    function upwork_has_valid_authorize_url()
    {
        $this->assertEquals('https://www.upwork.com/services/api/auth', $this->upwork->config()->authorizeUrl());
    }

    /** @test */
    function upwork_has_valid_access_token_url()
    {
        $this->assertEquals('https://www.upwork.com/api/auth/v1/oauth/token/access', $this->upwork->config()->accessTokenUrl());
    }

    /** @test */
    function upwork_has_valid_resource_base_url()
    {
        $this->assertEquals('https://www.upwork.com/', $this->upwork->config()->resourceBaseUrl());
    }
}
