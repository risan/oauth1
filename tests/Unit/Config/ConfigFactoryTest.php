<?php

namespace Risan\OAuth1\Test\Unit\Config;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Config\ConfigFactory;
use Risan\OAuth1\Config\ConfigInterface;
use Risan\OAuth1\Config\ConfigFactoryInterface;

class ConfigFactoryTest extends TestCase
{
    private $configFactory;
    private $config;

    function setUp()
    {
        $this->configFactory = new ConfigFactory;

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
    function it_implements_config_factory_interface()
    {
        $this->assertInstanceOf(ConfigFactoryInterface::class, $this->configFactory);
    }

    /** @test */
    function it_can_create_config_instance_from_array()
    {
        $config = $this->configFactory->createFromArray($this->config);

        $this->assertInstanceOf(ConfigInterface::class, $config);

        $this->assertEquals('client_id', $config->getClientCredentialsIdentifier());

        $this->assertEquals('client_secret', $config->getClientCredentialsSecret());

        $this->assertEquals('http://example.com/request_token', (string) $config->getTemporaryCredentialsUri());

        $this->assertEquals('http://example.com/authorize', (string) $config->getAuthorizationUri());

        $this->assertEquals('http://example.com/access_token', (string) $config->getTokenCredentialsUri());

        $this->assertTrue($config->hasCallbackUri());

        $this->assertEquals('http://johndoe.net', (string) $config->getCallbackUri());
    }

    /** @test */
    function it_throws_exception_if_client_credentials_identifier_is_missing()
    {
        unset($this->config['client_credentials_identifier']);

        $this->expectException(InvalidArgumentException::class);
        $this->configFactory->createFromArray($this->config);
    }

    /** @test */
    function it_throws_exception_if_client_credentials_secret_is_missing()
    {
        unset($this->config['client_credentials_secret']);

        $this->expectException(InvalidArgumentException::class);
        $this->configFactory->createFromArray($this->config);
    }

    /** @test */
    function it_throws_exception_if_temporary_credentials_uri_is_missing()
    {
        unset($this->config['temporary_credentials_uri']);

        $this->expectException(InvalidArgumentException::class);
        $this->configFactory->createFromArray($this->config);
    }

     /** @test */
    function it_throws_exception_if_authorization_uri_is_missing()
    {
        unset($this->config['authorization_uri']);

        $this->expectException(InvalidArgumentException::class);
        $this->configFactory->createFromArray($this->config);
    }

     /** @test */
    function it_throws_exception_if_token_credentials_uri_is_missing()
    {
        unset($this->config['token_credentials_uri']);

        $this->expectException(InvalidArgumentException::class);
        $this->configFactory->createFromArray($this->config);
    }
}
