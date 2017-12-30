<?php

namespace Risan\OAuth1\Test\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Risan\OAuth1\OAuth1Interface;
use Risan\OAuth1\ProviderFactory;
use Risan\OAuth1\Provider\Twitter;

class ProviderFactoryTest extends TestCase
{
    private $config;
    private $twitter;

    function setUp()
    {
        $this->config = [
            'client_credentials_identifier' => 'client_id',
            'client_credentials_secret' => 'client_secret',
            'callback_uri' => 'http://johndoe.net',
        ];

        $this->twitter = new Twitter();
    }

    /** @test */
    function it_can_create_oauth1_instance()
    {
        $oauth1 = ProviderFactory::create($this->twitter, $this->config);

        $this->assertInstanceOf(OAuth1Interface::class, $oauth1);
    }
    
    /** @test */
    function it_can_create_oauth1_provider_instance_dynamically()
    {
        $oauth1 = ProviderFactory::twitter($this->config);

        $this->assertInstanceOf(OAuth1Interface::class, $oauth1);
    }
    
    /** @test */
    function it_throws_exception_if_provider_not_exists()
    {
        $this->expectException(InvalidArgumentException::class);

        ProviderFactory::invalid($this->config);
    }
    
    /** @test */
    function it_throws_exception_if_config_is_not_passed()
    {
        $this->expectException(InvalidArgumentException::class);

        ProviderFactory::twitter();
    }

    /** @test */
    function it_throws_exception_if_config_is_not_an_array()
    {
        $this->expectException(InvalidArgumentException::class);

        ProviderFactory::invalid('foo');
    }
}
