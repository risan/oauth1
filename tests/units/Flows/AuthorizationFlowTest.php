<?php

use Risan\OAuth1\OAuth1;
use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Tokens\RequestToken;

class AuthorizationFlowTest extends TestCase {
    protected $config;
    protected $oauth1;
    protected $requestToken;

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

        $this->requestToken = new RequestToken('foo', 'bar');
    }

    /** @test */
    function oauth1_has_authorize_url()
    {
        return $this->assertEquals($this->config['authorize_url'], $this->oauth1->authorizeUrl());
    }

    /** @test */
    function oauth1_can_build_authorization_url()
    {
        $expected = $this->config['authorize_url'] . '?oauth_token=' . $this->requestToken->key();

        return $this->assertEquals($expected, $this->oauth1->buildAuthorizationUrl($this->requestToken));
    }
}
