<?php

namespace Risan\OAuth1\Test\Unit;

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\OAuth1Factory;
use Risan\OAuth1\OAuth1Interface;

class OAuth1FactoryTest extends TestCase
{
    private $config;

    function setUp()
    {
        $this->config = [
            'client_credentials_identifier' => 'client_id',
            'client_credentials_secret' => 'client_secret',
            'base_uri' => 'http://example.com',
            'temporary_credentials_uri' => '/request_token',
            'authorization_uri' => '/authorize',
            'token_credentials_uri' => '/access_token',
            'callback_uri' => 'http://johndoe.net',
        ];
    }

    /** @test */
    function it_can_create_oauth1_instance()
    {
        $oauth1 = OAuth1Factory::create($this->config);

        $this->assertInstanceOf(OAuth1Interface::class, $oauth1);
    }
}
