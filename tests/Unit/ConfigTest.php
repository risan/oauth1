<?php

namespace Risan\OAuth1\Test\Unit;

use Risan\OAuth1\Config;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Risan\OAuth1\ConfigInterface;
use Risan\OAuth1\Credentials\ClientCredentials;

class ConfigTest extends TestCase
{
    private $clientCredentialsIdentifier;
    private $clientCredentialsSecret;
    private $clientCredentials;
    private $temporaryCredentialsUrl;
    private $authorizationUrl;
    private $tokenCredentialsUrl;
    private $callbackUri;
    private $configParams;
    private $config;
    private $configWithoutCallbackUri;

    function setUp()
    {
        $this->clientCredentialsIdentifier = 'client_id';
        $this->clientCredentialsSecret = 'client_secret';
        $this->clientCredentials = new ClientCredentials($this->clientCredentialsIdentifier, $this->clientCredentialsSecret);
        $this->temporaryCredentialsUrl = 'http://example.com/request_token';
        $this->authorizationUrl = 'http://example.com/authorize';
        $this->tokenCredentialsUrl = 'http://example.com/access_token';
        $this->callbackUri = 'http://johndoe.com/callback_uri';

        $this->configParams = [
            'client_credentials_identifier' => $this->clientCredentialsIdentifier,
            'client_credentials_secret' => $this->clientCredentialsSecret,
            'temporary_credentials_url' => $this->temporaryCredentialsUrl,
            'authorization_url' => $this->authorizationUrl,
            'token_credentials_url' => $this->tokenCredentialsUrl,
            'callback_uri' => $this->callbackUri,
        ];

        $this->config = new Config(
            $this->clientCredentials,
            $this->temporaryCredentialsUrl,
            $this->authorizationUrl,
            $this->tokenCredentialsUrl,
            $this->callbackUri
        );

        $this->configWithoutCallbackUri = new Config(
            $this->clientCredentials,
            $this->temporaryCredentialsUrl,
            $this->authorizationUrl,
            $this->tokenCredentialsUrl,
            null
        );
    }

    /** @test */
    function config_is_an_instance_of_config_interface()
    {
        $this->assertInstanceOf(ConfigInterface::class, $this->config);
    }

    /** @test */
    function config_can_get_client_credentials()
    {
        $this->assertInstanceOf(ClientCredentials::class, $this->config->getClientCredentials());

        $this->assertSame($this->clientCredentials, $this->config->getClientCredentials());
    }

    /** @test */
    function config_can_get_client_credentials_identifier()
    {
        $this->assertEquals(
            $this->clientCredentials->getIdentifier(),
            $this->config->getClientCredentialsIdentifier()
        );
    }

    /** @test */
    function config_can_get_client_credentials_secret()
    {
        $this->assertEquals(
            $this->clientCredentials->getSecret(),
            $this->config->getClientCredentialsSecret()
        );
    }

    /** @test */
    function config_can_check_if_callback_uri_exists()
    {
        $this->assertTrue($this->config->hasCallbackUri());

        $this->assertFalse($this->configWithoutCallbackUri->hasCallbackUri());
    }

    /** @test */
    function config_can_get_callback_uri()
    {
        $this->assertEquals($this->callbackUri, $this->config->getCallbackUri());
    }

    /** @test */
    function config_can_get_temporary_credentials_url()
    {
        $this->assertEquals($this->temporaryCredentialsUrl, $this->config->getTemporaryCredentialsUrl());
    }

    /** @test */
    function config_can_get_authorization_url()
    {
        $this->assertEquals($this->authorizationUrl, $this->config->getAuthorizationUrl());
    }

    /** @test */
    function config_can_get_token_credentials_url()
    {
        $this->assertEquals($this->tokenCredentialsUrl, $this->config->getTokenCredentialsUrl());
    }

    /** @test */
    function config_can_create_from_array()
    {
        $config = Config::createFromArray($this->configParams);

        $this->assertInstanceOf(Config::class, $config);

        $this->assertInstanceOf(ClientCredentials::class, $config->getClientCredentials());

        $this->assertEquals(
            $this->configParams['client_credentials_identifier'],
            $config->getClientCredentialsIdentifier()
        );

        $this->assertEquals(
            $this->configParams['client_credentials_secret'],
            $config->getClientCredentialsSecret()
        );

        $this->assertEquals(
            $this->configParams['temporary_credentials_url'],
            $config->getTemporaryCredentialsUrl()
        );

        $this->assertEquals(
            $this->configParams['authorization_url'],
            $config->getAuthorizationUrl()
        );

        $this->assertEquals(
            $this->configParams['token_credentials_url'],
            $config->getTokenCredentialsUrl()
        );

        $this->assertEquals(
            $this->configParams['callback_uri'],
            $config->getCallbackUri()
        );
    }

    /** @test */
    function config_must_throw_exception_if_client_credentials_identifier_is_empty()
    {
        $this->expectException(InvalidArgumentException::class);

        Config::createFromArray([
            'client_credentials_secret' => $this->clientCredentialsSecret,
            'temporary_credentials_url' => $this->temporaryCredentialsUrl,
            'authorization_url' => $this->authorizationUrl,
            'token_credentials_url' => $this->tokenCredentialsUrl,
            'callback_uri' => $this->callbackUri,
        ]);
    }

    /** @test */
    function config_must_throw_exception_if_client_credentials_secret_is_empty()
    {
        $this->expectException(InvalidArgumentException::class);

        Config::createFromArray([
            'client_credentials_identifier' => $this->clientCredentialsIdentifier,
            'temporary_credentials_url' => $this->temporaryCredentialsUrl,
            'authorization_url' => $this->authorizationUrl,
            'token_credentials_url' => $this->tokenCredentialsUrl,
            'callback_uri' => $this->callbackUri,
        ]);
    }

    /** @test */
    function config_must_throw_exception_if_temporary_credentials_url_is_empty()
    {
        $this->expectException(InvalidArgumentException::class);

        Config::createFromArray([
            'authorization_url' => $this->authorizationUrl,
            'token_credentials_url' => $this->tokenCredentialsUrl,
            'client_credentials_identifier' => $this->clientCredentialsIdentifier,
            'client_credentials_secret' => $this->clientCredentialsSecret,
            'callback_uri' => $this->callbackUri,
        ]);
    }

    /** @test */
    function config_must_throw_exception_if_authorization_url_is_empty()
    {
        $this->expectException(InvalidArgumentException::class);

        Config::createFromArray([
            'temporary_credentials_url' => $this->temporaryCredentialsUrl,
            'client_credentials_identifier' => $this->clientCredentialsIdentifier,
            'client_credentials_secret' => $this->clientCredentialsSecret,
            'token_credentials_url' => $this->tokenCredentialsUrl,
            'callback_uri' => $this->callbackUri,
        ]);
    }

    /** @test */
    function config_must_throw_exception_if_token_credentials_url_is_empty()
    {
        $this->expectException(InvalidArgumentException::class);

        Config::createFromArray([
            'authorization_url' => $this->authorizationUrl,
            'temporary_credentials_url' => $this->temporaryCredentialsUrl,
            'client_credentials_identifier' => $this->clientCredentialsIdentifier,
            'client_credentials_secret' => $this->clientCredentialsSecret,
            'callback_uri' => $this->callbackUri,
        ]);
    }

    /** @test */
    function config_can_be_created_without_callback_uri()
    {
        $config = Config::createFromArray([
            'client_credentials_identifier' => $this->clientCredentialsIdentifier,
            'client_credentials_secret' => $this->clientCredentialsSecret,
            'authorization_url' => $this->authorizationUrl,
            'token_credentials_url' => $this->tokenCredentialsUrl,
            'temporary_credentials_url' => $this->temporaryCredentialsUrl,
        ]);

        $this->assertInstanceOf(Config::class, $config);
    }
}
