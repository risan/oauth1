<?php

declare(strict_types=1);

namespace Risan\OAuth1\Test\Unit\Config;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;
use Risan\OAuth1\Config\Config;
use Risan\OAuth1\Config\ConfigInterface;
use Risan\OAuth1\Config\UriConfig;
use Risan\OAuth1\Credentials\ClientCredentials;
use Risan\OAuth1\Request\UriParser;

class ConfigTest extends TestCase
{
    private $config;

    private $clientCredentials;

    private $uriParser;

    private $uriConfig;

    protected function setUp(): void
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

    #[Test]
    public function it_implements_config_interface()
    {
        $this->assertInstanceOf(ConfigInterface::class, $this->config);
    }

    #[Test]
    public function it_can_get_client_credentials()
    {
        $this->assertSame($this->clientCredentials, $this->config->getClientCredentials());
    }

    #[Test]
    public function it_can_get_client_credentials_identifier()
    {
        $this->assertEquals('client_id', $this->config->getClientCredentialsIdentifier());
    }

    #[Test]
    public function it_can_get_client_credentials_secret()
    {
        $this->assertEquals('client_secret', $this->config->getClientCredentialsSecret());
    }

    #[Test]
    public function it_can_get_uri()
    {
        $this->assertSame($this->uriConfig, $this->config->getUri());
    }

    #[Test]
    public function it_can_get_temporary_credentials_uri()
    {
        $uri = $this->config->getTemporaryCredentialsUri();
        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertEquals('http://example.com/request_token', (string) $uri);
    }

    #[Test]
    public function it_can_get_authorization_uri()
    {
        $uri = $this->config->getAuthorizationUri();
        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertEquals('http://example.com/authorize', (string) $uri);
    }

    #[Test]
    public function it_can_get_token_credentials_uri()
    {
        $uri = $this->config->getTokenCredentialsUri();
        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertEquals('http://example.com/access_token', (string) $uri);
    }

    #[Test]
    public function it_can_get_credential_endpoint_methods()
    {
        $this->assertSame('POST', $this->config->getTemporaryCredentialsMethod());
        $this->assertSame('POST', $this->config->getTokenCredentialsMethod());

        $config = new Config($this->clientCredentials, $this->uriConfig, 'GET', 'PUT');
        $this->assertSame('GET', $config->getTemporaryCredentialsMethod());
        $this->assertSame('PUT', $config->getTokenCredentialsMethod());
    }

    #[Test]
    public function it_can_get_callback_uri()
    {
        $uri = $this->config->getCallbackUri();
        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertEquals('http://johndoe.com', (string) $uri);
    }

    #[Test]
    public function it_can_check_if_callback_uri_is_set()
    {
        $this->assertTrue($this->config->hasCallbackUri());
    }

    #[Test]
    public function it_can_build_uri()
    {
        // Resolve relative URI.
        $uri = $this->config->buildUri('/foo');
        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertEquals('http://example.com/foo', (string) $uri);

        // Resolve absolute URI.
        $uri = $this->uriConfig->build('http://example.net/foo');
        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertEquals('http://example.net/foo', (string) $uri);

        // Missing scheme.
        $missingScheme = $this->uriParser->toPsrUri('http://example.net')->withScheme('');
        $uri = $this->uriConfig->build($missingScheme);
        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertEquals('http://example.net', (string) $uri);
    }
}
