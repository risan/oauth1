<?php

use Risan\OAuth1\OAuth1;
use PHPUnit\Framework\TestCase;
use Risan\OAuth1\OAuth1Factory;

class OAuth1FactoryTest extends TestCase
{
    private $configParams;

    function setUp()
    {
        $this->configParams = [
            'client_credentials_identifier' => 'client_id',
            'client_credentials_secret' => 'client_secret',
            'temporary_credentials_url' => 'http://example.com/request_token',
            'authorization_url' => 'http://example.com/authorize',
            'token_credentials_url' => 'http://example.com/access_token',
            'callback_uri' => 'http://example.com/callback_uri',
        ];
    }

    /** @test */
    function oauth1_factory_can_create_oauth1_instance()
    {
        $this->assertInstanceOf(OAuth1::class, OAuth1Factory::create($this->configParams));
    }
}
