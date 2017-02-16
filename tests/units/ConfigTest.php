<?php

use OAuth1\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase {
    protected $config;

    function setUp()
    {
        $this->config = new Config(
            'key',
            'secret',
            'http://requesttoken.foo',
            'http://authorize.foo',
            'http://accesstoken.foo',
            'http://callback.foo',
            'http://resource.foo'
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
    function config_has_request_token_url()
    {
        $this->assertEquals('http://requesttoken.foo', $this->config->requestTokenUrl());
    }

    /** @test */
    function config_has_authorize_url()
    {
        $this->assertEquals('http://authorize.foo', $this->config->authorizeUrl());
    }

    /** @test */
    function config_has_access_token_url()
    {
        $this->assertEquals('http://accesstoken.foo', $this->config->accessTokenUrl());
    }

    /** @test */
    function config_has_callback_url()
    {
        $this->assertEquals('http://callback.foo', $this->config->callbackUrl());
    }

    /** @test */
    function config_has_resource_base_url()
    {
        $this->assertEquals('http://resource.foo', $this->config->resourceBaseUrl());
    }

    /** @test */
    function config_can_set_resource_base_url()
    {
        $this->assertInstanceOf(Config::class, $this->config->setResourceBaseUrl('http://resource.bar'));
        $this->assertEquals('http://resource.bar', $this->config->resourceBaseUrl());
    }

    /** @test */
    function config_can_be_created_from_array()
    {
        $params = [
            'consumer_key' => 'key',
            'consumer_secret' => 'secret',
            'request_token_url' => 'http://requesttoken.foo',
            'authorize_url' => 'http://authorize.foo',
            'access_token_url' => 'http://accesstoken.foo',
            'callback_url' => 'http://callback.foo',
            'resource_base_url' => 'http://resource.foo'
        ];

        $config = Config::fromArray($params);

        $this->assertInstanceOf(Config::class, $config);
        $this->assertEquals('key', $config->consumerKey());
        $this->assertEquals('secret', $config->consumerSecret());
        $this->assertEquals('http://requesttoken.foo', $config->requestTokenUrl());
        $this->assertEquals('http://authorize.foo', $config->authorizeUrl());
        $this->assertEquals('http://accesstoken.foo', $config->accessTokenUrl());
        $this->assertEquals('http://callback.foo', $config->callbackUrl());
        $this->assertEquals('http://resource.foo', $config->resourceBaseUrl());
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    function config_without_consumer_key_throws_exception()
    {
        Config::fromArray([
            'consumer_secret' => 'secret',
            'request_token_url' => 'http://requesttoken.foo',
            'authorize_url' => 'http://authorize.foo',
            'access_token_url' => 'http://accesstoken.foo',
            'callback_url' => 'http://callback.foo',
            'resource_base_url' => 'http://resource.foo'
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
            'request_token_url' => 'http://requesttoken.foo',
            'authorize_url' => 'http://authorize.foo',
            'access_token_url' => 'http://accesstoken.foo',
            'callback_url' => 'http://callback.foo',
            'resource_base_url' => 'http://resource.foo'
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
            'authorize_url' => 'http://authorize.foo',
            'access_token_url' => 'http://accesstoken.foo',
            'callback_url' => 'http://callback.foo',
            'resource_base_url' => 'http://resource.foo'
        ]);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    function config_without_authorize_url_throws_exception()
    {
        Config::fromArray([
            'consumer_key' => 'key',
            'consumer_secret' => 'secret',
            'request_token_url' => 'http://requesttoken.foo',
            'access_token_url' => 'http://accesstoken.foo',
            'callback_url' => 'http://callback.foo',
            'resource_base_url' => 'http://resource.foo'
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
            'request_token_url' => 'http://requesttoken.foo',
            'authorize_url' => 'http://authorize.foo',
            'callback_url' => 'http://callback.foo',
            'resource_base_url' => 'http://resource.foo'
        ]);
    }

    /** @test */
    function config_created_without_callback_url()
    {
        $config = Config::fromArray([
            'consumer_key' => 'key',
            'consumer_secret' => 'secret',
            'request_token_url' => 'http://requesttoken.foo',
            'authorize_url' => 'http://authorize.foo',
            'access_token_url' => 'http://accesstoken.foo',
            'resource_base_url' => 'http://resource.foo'
        ]);

        $this->assertInstanceOf(Config::class, $config);
        $this->assertEmpty($config->callbackUrl());
    }

    /** @test */
    function config_created_without_resource_base_url()
    {
        $config = Config::fromArray([
            'consumer_key' => 'key',
            'consumer_secret' => 'secret',
            'request_token_url' => 'http://requesttoken.foo',
            'authorize_url' => 'http://authorize.foo',
            'access_token_url' => 'http://accesstoken.foo',
            'callback_url' => 'http://callback.foo'
        ]);

        $this->assertInstanceOf(Config::class, $config);
        $this->assertEmpty($config->resourceBaseUrl());
    }
}
