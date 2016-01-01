<?php

use OAuth1\OAuth1;
use OAuth1\Contracts\Tokens\RequestTokenInterface;

class OAuth1RequestTokenTest extends PHPUnit_Framework_TestCase {
    protected $config;
    protected $oauth1;

    function setUp()
    {
        $this->config = [
            'consumer_key' => 'key',
            'consumer_secret' => 'secret',
            'request_token_url' => 'http://www.mocky.io/v2/567a64390f0000eb051aef7c',
            'authorize_url' => 'http://authorize.foo',
            'access_token_url' => 'http://www.mocky.io/v2/567a64390f0000eb051aef7c',
            'callback_url' => 'http://callback.foo',
            'resource_base_url' => 'http://www.mocky.io/v2/'
        ];

        $this->oauth1 = new OAuth1($this->config);
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
}
