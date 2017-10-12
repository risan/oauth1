<?php

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Request\RequestConfigFactory;
use Risan\OAuth1\Request\RequestConfigInterface;

class RequestConfigFactoryTest extends TestCase
{
    function setUp()
    {
        $this->configParams = [
            'client_credentials_identifier' => 'client_id',
            'client_credentials_secret' => 'client_secret',
            'temporary_credentials_url' => 'http://example.com/temporary_credentials_url',
            'callback_uri' => 'http://example.com/callback_uri',
        ];
    }

    /** @test */
    function request_config_factory_can_create_request_config_instance()
    {
        $this->assertInstanceOf(
            RequestConfigInterface::class,
            RequestConfigFactory::create($this->configParams)
        );
    }
}
