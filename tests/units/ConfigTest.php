<?php

use OAuth1\Config;

class ConfigTest extends PHPUnit_Framework_TestCase {
    protected $config;

    function setUp()
    {
        $this->config = new Config(
            'key',
            'secret',
            'http://callback.foo',
            'http://requesttoken.foo',
            'http://accesstoken.foo'
        );
    }

    /** @test */
    function config_has_consumer_key()
    {
        $this->assertEquals('key', $this->config->consumerKey());
    }

    /** @test */
    function config_has_consumer_secret()
    {
        $this->assertEquals('secret', $this->config->consumerSecret());
    }

    /** @test */
    function config_has_callback_url()
    {
        $this->assertEquals('http://callback.foo', $this->config->callbackUrl());
    }

    /** @test */
    function config_has_request_token_url()
    {
        $this->assertEquals('http://requesttoken.foo', $this->config->requestTokenUrl());
    }

    /** @test */
    function config_has_access_token_url()
    {
        $this->assertEquals('http://accesstoken.foo', $this->config->accessTokenUrl());
    }

    /** @test */
    function config_can_be_created_from_array()
    {
        $params = [
            'consumer_key' => 'key',
            'consumer_secret' => 'secret',
            'callback_url' => 'http://callback.foo',
            'request_token_url' => 'http://requesttoken.foo',
            'access_token_url' => 'http://accesstoken.foo'
        ];

        $config = Config::fromArray($params);

        $this->assertInstanceOf(Config::class, $config);
        $this->assertEquals('key', $config->consumerKey());
        $this->assertEquals('secret', $config->consumerSecret());
        $this->assertEquals('http://callback.foo', $config->callbackUrl());
        $this->assertEquals('http://requesttoken.foo', $config->requestTokenUrl());
        $this->assertEquals('http://accesstoken.foo', $config->accessTokenUrl());
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    function config_without_consumer_key_throws_exception()
    {
        Config::fromArray([
            'consumer_secret' => 'secret',
            'callback_url' => 'http://callback.foo',
            'request_token_url' => 'http://requesttoken.foo',
            'access_token_url' => 'http://accesstoken.foo'
        ]);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    function config_without_consumer_secret_throws_exception()
    {
        Config::fromArray([
            'consumer_key' => 'key',
            'callback_url' => 'http://callback.foo',
            'request_token_url' => 'http://requesttoken.foo',
            'access_token_url' => 'http://accesstoken.foo'
        ]);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    function config_without_request_token_url_throws_exception()
    {
        Config::fromArray([
            'consumer_key' => 'key',
            'consumer_secret' => 'secret',
            'callback_url' => 'http://callback.foo',
            'access_token_url' => 'http://accesstoken.foo'
        ]);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    function config_without_access_token_url_throws_exception()
    {
        Config::fromArray([
            'consumer_key' => 'key',
            'consumer_secret' => 'secret',
            'callback_url' => 'http://callback.foo',
            'request_token_url' => 'http://requesttoken.foo'
        ]);
    }

    /** @test */
    function config_created_without_callback_url()
    {
        $config = Config::fromArray([
            'consumer_key' => 'key',
            'consumer_secret' => 'secret',
            'request_token_url' => 'http://requesttoken.foo',
            'access_token_url' => 'http://accesstoken.foo'
        ]);

        $this->assertInstanceOf(Config::class, $config);
        $this->assertEmpty($config->callbackUrl());
    }
}
