<?php

use OAuth1\OAuth1;
use OAuth1\Tokens\AccessToken;
use Psr\Http\Message\ResponseInterface;
use OAuth1\Contracts\Tokens\AccessTokenInterface;

class OAuth1GrantedTest extends PHPUnit_Framework_TestCase {
    protected $config;
    protected $oauth1;
    protected $accessToken;
    protected $resourcePath;

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

        $this->accessToken = new AccessToken('foo', 'bar');

        $this->resourcePath = '567c65e9100000612b7f3084';
    }

    /** @test */
    function oauth1_has_resource_base_url()
    {
        $this->assertEquals($this->config['resource_base_url'], $this->oauth1->resourceBaseUrl());
    }

    /** @test */
    function oauth1_can_set_resource_base_url()
    {
        $this->assertInstanceOf(OAuth1::class, $this->oauth1->setResourceBaseUrl('http://resource.foo'));
        $this->assertEquals('http://resource.foo', $this->oauth1->resourceBaseUrl());
        $this->oauth1->setResourceBaseUrl($this->config['resource_base_url']);
    }

    /** @test */
    function oauth1_can_generate_resource_url()
    {
        $expected = $this->oauth1->resourceBaseUrl() . $this->resourcePath;

        $this->assertEquals($expected, $this->oauth1->resourceUrl($this->resourcePath));
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    function oauth1_throws_exception_when_send_request_without_granted_access_token()
    {
        $this->oauth1->request('GET', $this->resourcePath);
    }

    /** @test */
    function oauth1_can_send_http_request()
    {
        $this->oauth1->setGrantedAccessToken($this->accessToken);

        $this->assertInstanceOf(ResponseInterface::class, $this->oauth1->request('GET', $this->resourcePath));
    }

    /** @test */
    function oauth1_can_send_http_get_request()
    {
        $this->oauth1->setGrantedAccessToken($this->accessToken);

        $this->assertInstanceOf(ResponseInterface::class, $this->oauth1->get($this->resourcePath));
    }

    /** @test */
    function oauth1_can_send_http_post_request()
    {
        $this->oauth1->setGrantedAccessToken($this->accessToken);

        $this->assertInstanceOf(ResponseInterface::class, $this->oauth1->post($this->resourcePath));
    }

    /** @test */
    function oauth1_can_send_http_put_request()
    {
        $this->oauth1->setGrantedAccessToken($this->accessToken);

        $this->assertInstanceOf(ResponseInterface::class, $this->oauth1->put($this->resourcePath));
    }

    /** @test */
    function oauth1_can_send_http_patch_request()
    {
        $this->oauth1->setGrantedAccessToken($this->accessToken);

        $this->assertInstanceOf(ResponseInterface::class, $this->oauth1->patch($this->resourcePath));
    }

    /** @test */
    function oauth1_can_send_http_delete_request()
    {
        $this->oauth1->setGrantedAccessToken($this->accessToken);

        $this->assertInstanceOf(ResponseInterface::class, $this->oauth1->delete($this->resourcePath));
    }

    /** @test */
    function oauth1_can_send_http_head_request()
    {
        $this->oauth1->setGrantedAccessToken($this->accessToken);

        $this->assertInstanceOf(ResponseInterface::class, $this->oauth1->head($this->resourcePath));
    }

    /** @test */
    function oauth1_can_send_http_options_request()
    {
        $this->oauth1->setGrantedAccessToken($this->accessToken);

        $this->assertInstanceOf(ResponseInterface::class, $this->oauth1->options($this->resourcePath));
    }

    /** @test */
    function oauth1_can_get_and_set_granted_access_token()
    {
        $this->assertInstanceOf(OAuth1::class, $this->oauth1->setGrantedAccessToken($this->accessToken));

        $this->assertInstanceOf(AccessTokenInterface::class, $this->oauth1->grantedAccessToken());
    }

    /** @test */
    function oauth1_can_generate_granted_request_headers()
    {
        $this->assertArrayHasKey('Authorization', $this->oauth1->grantedRequestHeaders(
            $this->accessToken,
            $this->oauth1->resourceUrl($this->resourcePath),
            'GET'
        ));
    }
}
