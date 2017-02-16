<?php

use OAuth1\Providers\Tumblr;
use PHPUnit\Framework\TestCase;
use OAuth1\Contracts\ConfigInterface;
use OAuth1\Contracts\HttpClientInterface;
use OAuth1\Contracts\Signers\HmacSha1SignerInterface;

class TumblrTest extends TestCase {
    protected $config;
    protected $tumblr;

    function setUp()
    {
        $this->config = [
            'consumer_key' => 'key',
            'consumer_secret' => 'secret',
            'callback_url' => 'http://callback.foo'
        ];

        $this->tumblr = new Tumblr($this->config);
    }

    /** @test */
    function tumblr_has_default_config()
    {
        $this->assertCount(4, $this->tumblr->defaultConfig());
    }

    /** @test */
    function tumblr_has_http_client()
    {
        $this->assertInstanceOf(HttpClientInterface::class, $this->tumblr->httpClient());
    }

    /** @test */
    function tumblr_has_config()
    {
        $this->assertInstanceOf(ConfigInterface::class, $this->tumblr->config());
    }

    /** @test */
    function tumblr_has_hmac_sha1_signer()
    {
        $this->assertInstanceOf(HmacSha1SignerInterface::class, $this->tumblr->signer());
    }

    /** @test */
    function tumblr_has_valid_request_token_url()
    {
        $this->assertEquals('https://www.tumblr.com/oauth/request_token', $this->tumblr->config()->requestTokenUrl());
    }

    /** @test */
    function tumblr_has_valid_authorize_url()
    {
        $this->assertEquals('https://www.tumblr.com/oauth/authorize', $this->tumblr->config()->authorizeUrl());
    }

    /** @test */
    function tumblr_has_valid_access_token_url()
    {
        $this->assertEquals('https://www.tumblr.com/oauth/access_token', $this->tumblr->config()->accessTokenUrl());
    }

    /** @test */
    function tumblr_has_valid_resource_base_url()
    {
        $this->assertEquals('https://api.tumblr.com/v2/', $this->tumblr->config()->resourceBaseUrl());
    }
}
