<?php

use OAuth1\Config;

class ConfigTest extends PHPUnit_Framework_TestCase {
    protected $config;

    function setUp()
    {
        $this->config = new Config(
            'key',
            'secret',
            'http://requesttoken.foo',
            'http://authorization.foo',
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
    function config_has_authorization_url()
    {
        $this->assertEquals('http://authorization.foo', $this->config->authorizationUrl());
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
    function config_can_be_created_from_array()
    {
        $params = [
            'consumer_key' => 'key',
            'consumer_secret' => 'secret',
            'request_token_url' => 'http://requesttoken.foo',
            'authorization_url' => 'http://authorization.foo',
            'access_token_url' => 'http://accesstoken.foo',
            'callback_url' => 'http://callback.foo',
            'resource_base_url' => 'http://resource.foo'
        ];

        $config = Config::fromArray($params);

        $this->assertInstanceOf(Config::class, $config);
        $this->assertEquals('key', $config->consumerKey());
        $this->assertEquals('secret', $config->consumerSecret());
        $this->assertEquals('http://requesttoken.foo', $config->requestTokenUrl());
        $this->assertEquals('http://authorization.foo', $config->authorizationUrl());
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
            'authorization_url' => 'http://authorization.foo',
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
            'authorization_url' => 'http://authorization.foo',
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
            'authorization_url' => 'http://authorization.foo',
            'access_token_url' => 'http://accesstoken.foo',
            'callback_url' => 'http://callback.foo',
            'resource_base_url' => 'http://resource.foo'
        ]);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    function config_without_authorization_url_throws_exception()
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
            'authorization_url' => 'http://authorization.foo',
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
            'authorization_url' => 'http://authorization.foo',
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
            'authorization_url' => 'http://authorization.foo',
            'access_token_url' => 'http://accesstoken.foo',
            'callback_url' => 'http://callback.foo'
        ]);

        $this->assertInstanceOf(Config::class, $config);
        $this->assertEmpty($config->resourceBaseUrl());
    }
}
