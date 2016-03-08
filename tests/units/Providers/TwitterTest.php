<?php

use OAuth1\Providers\Twitter;
use OAuth1\Contracts\ConfigInterface;
use OAuth1\Contracts\HttpClientInterface;
use OAuth1\Contracts\Signers\HmacSha1SignerInterface;

class TwitterTest extends PHPUnit_Framework_TestCase {
    protected $config;
    protected $twitter;

    function setUp()
    {
        $this->config = [
            'consumer_key' => 'key',
            'consumer_secret' => 'secret',
            'callback_url' => 'http://callback.foo'
        ];

        $this->twitter = new Twitter($this->config);
    }

    /** @test */
    function twitter_has_http_client()
    {
        $this->assertInstanceOf(HttpClientInterface::class, $this->twitter->httpClient());
    }

    /** @test */
    function twitter_has_config()
    {
        $this->assertInstanceOf(ConfigInterface::class, $this->twitter->config());
    }

    /** @test */
    function twitter_has_hmac_sha1_signer()
    {
        $this->assertInstanceOf(HmacSha1SignerInterface::class, $this->twitter->signer());
    }

    /** @test */
    function twitter_has_valid_request_token_url()
    {
        $this->assertEquals('https://api.twitter.com/oauth/request_token', $this->twitter->config()->requestTokenUrl());
    }

    /** @test */
    function twitter_has_valid_authorize_url()
    {
        $this->assertEquals('https://api.twitter.com/oauth/authorize', $this->twitter->config()->authorizeUrl());
    }

    /** @test */
    function twitter_has_valid_access_token_url()
    {
        $this->assertEquals('https://api.twitter.com/oauth/access_token', $this->twitter->config()->accessTokenUrl());
    }

    /** @test */
    function twitter_has_valid_resource_base_url()
    {
        $this->assertEquals('https://api.twitter.com/1.1/', $this->twitter->config()->resourceBaseUrl());
    }
}
