<?php

use OAuth1\OAuth1;
use OAuth1\Tokens\RequestToken;
use OAuth1\Contracts\ConfigInterface;
use OAuth1\Contracts\Tokens\AccessTokenInterface;
use OAuth1\Contracts\Tokens\RequestTokenInterface;

class OAuth1GrantedTest extends PHPUnit_Framework_TestCase {
    protected $config;
    protected $oauth1;
    protected $requestToken;

    function setUp()
    {
        $this->config = [
            'consumer_key' => 'key',
            'consumer_secret' => 'secret',
            'request_token_url' => 'http://www.mocky.io/v2/567a64390f0000eb051aef7c',
            'authorization_url' => 'http://authorization.foo',
            'access_token_url' => 'http://www.mocky.io/v2/567a64390f0000eb051aef7c',
            'callback_url' => 'http://callback.foo',
            'resource_base_url' => 'http://www.mocky.io/v2/'
        ];

        $this->oauth1 = new OAuth1($this->config);

        $this->requestToken = new RequestToken('foo', 'bar');
    }

    /** @test */
    function oauth1_has_resource_base_url()
    {
        return $this->assertEquals($this->config['resource_base_url'], $this->oauth1->resourceBaseUrl());
    }
}
