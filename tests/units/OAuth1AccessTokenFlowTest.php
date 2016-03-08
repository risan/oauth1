<?php

use OAuth1\OAuth1;
use OAuth1\Tokens\RequestToken;
use OAuth1\Contracts\Tokens\AccessTokenInterface;

class OAuth1AccessTokenFlowTest extends PHPUnit_Framework_TestCase {
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

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    function oauth1_throws_exception_when_token_key_is_invalid()
    {
        $this->oauth1->accessToken($this->requestToken, 'invalid', 'bar');
    }
}
