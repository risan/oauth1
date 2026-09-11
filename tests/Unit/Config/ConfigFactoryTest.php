<?php

declare(strict_types=1);

namespace Risan\OAuth1\Test\Unit\Config;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Config\ConfigFactory;
use Risan\OAuth1\Config\ConfigFactoryInterface;
use Risan\OAuth1\Config\ConfigInterface;

class ConfigFactoryTest extends TestCase
{
    private $configFactory;

    private $config;

    protected function setUp(): void
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

    #[Test]
    public function it_implements_config_factory_interface()
    {
        $this->assertInstanceOf(ConfigFactoryInterface::class, $this->configFactory);
    }

    #[Test]
    public function it_can_create_config_instance_from_array()
    {
        $config = $this->configFactory->createFromArray($this->config);

        $this->assertInstanceOf(ConfigInterface::class, $config);

        $this->assertEquals('client_id', $config->getClientCredentialsIdentifier());

        $this->assertEquals('client_secret', $config->getClientCredentialsSecret());

        $this->assertEquals('http://example.com/request_token', (string) $config->getTemporaryCredentialsUri());

        $this->assertEquals('http://example.com/authorize', (string) $config->getAuthorizationUri());

        $this->assertEquals('http://example.com/access_token', (string) $config->getTokenCredentialsUri());

        $this->assertSame('POST', $config->getTemporaryCredentialsMethod());
        $this->assertSame('POST', $config->getTokenCredentialsMethod());

        $this->assertTrue($config->hasCallbackUri());

        $this->assertEquals('http://johndoe.net', (string) $config->getCallbackUri());
    }

    #[Test]
    public function it_throws_exception_if_client_credentials_identifier_is_missing()
    {
        unset($this->config['client_credentials_identifier']);

        $this->expectException(InvalidArgumentException::class);
        $this->configFactory->createFromArray($this->config);
    }

    #[Test]
    public function it_throws_exception_if_client_credentials_identifier_is_empty()
    {
        $this->config['client_credentials_identifier'] = '';
        $this->expectException(InvalidArgumentException::class);

        $this->configFactory->createFromArray($this->config);
    }

    #[Test]
    public function it_throws_exception_if_client_credentials_secret_is_missing()
    {
        unset($this->config['client_credentials_secret']);

        $this->expectException(InvalidArgumentException::class);
        $this->configFactory->createFromArray($this->config);
    }

    #[Test]
    public function it_throws_exception_if_temporary_credentials_uri_is_missing()
    {
        unset($this->config['temporary_credentials_uri']);

        $this->expectException(InvalidArgumentException::class);
        $this->configFactory->createFromArray($this->config);
    }

    #[Test]
    public function it_throws_exception_if_authorization_uri_is_missing()
    {
        unset($this->config['authorization_uri']);

        $this->expectException(InvalidArgumentException::class);
        $this->configFactory->createFromArray($this->config);
    }

    #[Test]
    public function it_throws_exception_if_token_credentials_uri_is_missing()
    {
        unset($this->config['token_credentials_uri']);

        $this->expectException(InvalidArgumentException::class);
        $this->configFactory->createFromArray($this->config);
    }

    #[Test]
    public function it_throws_exception_if_callback_uri_is_missing()
    {
        unset($this->config['callback_uri']);

        $this->expectException(InvalidArgumentException::class);
        $this->configFactory->createFromArray($this->config);
    }

    #[Test]
    public function it_supports_the_out_of_band_callback_value()
    {
        $this->config['callback_uri'] = 'oob';

        $config = $this->configFactory->createFromArray($this->config);

        $this->assertSame('oob', (string) $config->getCallbackUri());
    }

    #[Test]
    public function it_throws_exception_if_a_required_uri_is_empty()
    {
        $this->config['token_credentials_uri'] = '';
        $this->expectException(InvalidArgumentException::class);

        $this->configFactory->createFromArray($this->config);
    }

    #[Test]
    public function it_normalizes_custom_credential_endpoint_methods()
    {
        $this->config['temporary_credentials_method'] = 'get';
        $this->config['token_credentials_method'] = 'put';

        $config = $this->configFactory->createFromArray($this->config);

        $this->assertSame('GET', $config->getTemporaryCredentialsMethod());
        $this->assertSame('PUT', $config->getTokenCredentialsMethod());
    }

    #[Test]
    public function it_rejects_an_invalid_credential_endpoint_method()
    {
        $this->config['token_credentials_method'] = 'GET /injected';
        $this->expectException(InvalidArgumentException::class);

        $this->configFactory->createFromArray($this->config);
    }
}
