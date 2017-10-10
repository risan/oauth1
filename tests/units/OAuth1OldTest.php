<?php

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\OAuth1Old as OAuth1;
use Risan\OAuth1\Contracts\ConfigInterface;
use Risan\OAuth1\Contracts\HttpClientInterface;
use Risan\OAuth1\Contracts\Signers\SignerInterface;

class OAuth1OldTest extends TestCase {
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

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    function oauth1_created_with_invalid_parameter_throws_exception()
    {
        new OAuth1('invalid');
    }

    /** @test */
    function oauth1_has_http_client()
    {
        $this->assertInstanceOf(HttpClientInterface::class, $this->oauth1->httpClient());
    }

    /** @test */
    function oauth1_has_config()
    {
        $this->assertInstanceOf(ConfigInterface::class, $this->oauth1->config());
    }

    /** @test */
    function oauth1_has_signer()
    {
        $this->assertInstanceOf(SignerInterface::class, $this->oauth1->signer());
    }

    /** @test */
    function oauth1_can_generate_nonce()
    {
        $nonce = $this->oauth1->nonce();

        $this->assertTrue(is_string($nonce));

        $this->assertEquals(32, strlen($nonce));
    }

    /** @test */
    function oauth1_can_get_timestamp()
    {
        $timestamp = $this->oauth1->timestamp();

        $this->assertLessThanOrEqual(time(), $timestamp);

        $this->assertGreaterThan(strtotime('-1 minute'), $timestamp);
    }

    /** @test */
    function oauth1_has_version()
    {
        $this->assertEquals('1.0', $this->oauth1->version());
    }

    /** @test */
    function oauth1_has_base_protocol_parameters()
    {
        $parameters = $this->oauth1->baseProtocolParameters();

        $this->assertArrayHasKey('oauth_consumer_key', $parameters);
        $this->assertArrayHasKey('oauth_nonce', $parameters);
        $this->assertArrayHasKey('oauth_signature_method', $parameters);
        $this->assertArrayHasKey('oauth_timestamp', $parameters);
        $this->assertArrayHasKey('oauth_version', $parameters);
    }

    /** @test */
    function oauth1_can_generate_authorization_headers()
    {
        $parameters = ['foo' => 'bar'];

        $this->assertEquals('OAuth foo=bar', $this->oauth1->authorizationHeaders($parameters));
    }
}
