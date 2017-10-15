<?php

namespace Risan\OAuth1\Test\Unit\Config;

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Config\Config;
use Psr\Http\Message\UriInterface;
use Risan\OAuth1\Config\UriConfig;
use Risan\OAuth1\Request\UriParser;
use Risan\OAuth1\Config\ConfigInterface;
use Risan\OAuth1\Credentials\ClientCredentials;

class ConfigTest extends TestCase
{
    private $config;
    private $clientCredentials;
    private $uriParser;
    private $uriConfig;

    function setUp()
    {
        $uris = [
            'base_uri' => 'http://example.com',
            'temporary_credentials_uri' => '/request_token',
            'authorization_uri' => '/authorize',
            'token_credentials_uri' => '/access_token',
            'callback_uri' => 'http://johndoe.com',
        ];

        $this->clientCredentials = new ClientCredentials('client_id', 'client_secret');
        $this->uriParser = new UriParser;
        $this->uriConfig = new UriConfig($uris, $this->uriParser);
        $this->config = new Config($this->clientCredentials, $this->uriConfig);
    }

    /** @test */
    function config_implements_config_interface()
    {
        $this->assertInstanceOf(ConfigInterface::class, $this->config);
    }

    /** @test */
    function config_can_get_client_credentials()
    {
        $this->assertSame($this->clientCredentials, $this->config->getClientCredentials());
    }

    /** @test */
    function config_can_get_client_credentials_identifier()
    {
        $this->assertEquals('client_id', $this->config->getClientCredentialsIdentifier());
    }

    /** @test */
    function config_can_get_client_credentials_secret()
    {
        $this->assertEquals('client_secret', $this->config->getClientCredentialsSecret());
    }

    /** @test */
    function config_can_get_uri()
    {
        $this->assertSame($this->uriConfig, $this->config->getUri());
    }

    /** @test */
    function config_can_get_temporary_credentials_uri()
    {
        $uri = $this->config->getTemporaryCredentialsUri();
        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertEquals('http://example.com/request_token', (string) $uri);
    }

    /** @test */
    function config_can_get_authorization_uri()
    {
        $uri = $this->config->getAuthorizationUri();
        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertEquals('http://example.com/authorize', (string) $uri);
    }

    /** @test */
    function config_can_get_token_credentials_uri()
    {
        $uri = $this->config->getTokenCredentialsUri();
        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertEquals('http://example.com/access_token', (string) $uri);
    }

    /** @test */
    function config_can_get_callback_uri()
    {
        $uri = $this->config->getCallbackUri();
        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertEquals('http://johndoe.com', (string) $uri);
    }

    /** @test */
    function config_can_check_if_callback_uri_is_set()
    {
        $this->assertTrue($this->config->hasCallbackUri());
    }

     /** @test */
    function config_can_check_if_callback_uri_is_not_set()
    {
        $uriConfig = new UriConfig([
            'base_uri' => 'http://example.com',
            'temporary_credentials_uri' => '/request_token',
            'authorization_uri' => '/authorize',
            'token_credentials_uri' => '/access_token',
        ], $this->uriParser);

        $config = new Config($this->clientCredentials, $uriConfig);

        $this->assertFalse($config->hasCallbackUri());
    }
}
