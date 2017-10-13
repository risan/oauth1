<?php

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\ConfigInterface;
use Risan\OAuth1\Request\RequestConfig;
use Risan\OAuth1\Signature\SignerInterface;
use Risan\OAuth1\Request\RequestConfigInterface;
use Risan\OAuth1\Request\NonceGeneratorInterface;
use Risan\OAuth1\Credentials\TemporaryCredentials;

class RequestConfigTest extends TestCase
{
    private $configStub;
    private $signerStub;
    private $nonceGeneratorStub;
    private $requestConfig;
    private $temporaryCredentialsStub;
    private $requestConfigStub;

    function setUp()
    {
        $this->configStub = $this->createMock(ConfigInterface::class);
        $this->signerStub = $this->createMock(SignerInterface::class);
        $this->nonceGeneratorStub = $this->createMock(NonceGeneratorInterface::class);
        $this->requestConfig = new RequestConfig($this->configStub, $this->signerStub, $this->nonceGeneratorStub);
        $this->temporaryCredentialsStub = $this->createMock(TemporaryCredentials::class);

        $this->requestConfigStub = $this->getMockBuilder(RequestConfig::class)
            ->setConstructorArgs([$this->configStub, $this->signerStub, $this->nonceGeneratorStub])
            ->setMethods([
                'getBaseProtocolParameters',
                'getTemporaryCredentialsUrl',
                'addSignatureParameter',
                'normalizeProtocolParameters',
            ])
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
    }

    /** @test */
    function request_config_is_an_instance_of_request_config_interface()
    {
        $this->assertInstanceOf(RequestConfigInterface::class, $this->requestConfig);
    }

    /** @test */
    function request_config_can_get_config()
    {
        $this->assertSame($this->configStub, $this->requestConfig->getConfig());
    }

    /** @test */
    function request_config_can_get_signer()
    {
        $this->assertSame($this->signerStub, $this->requestConfig->getSigner());
    }

    /** @test */
    function request_config_can_get_nonce_generator()
    {
        $this->assertSame($this->nonceGeneratorStub, $this->requestConfig->getNonceGenerator());
    }

    /** @test */
    function request_config_can_get_current_timestamp()
    {
        $this->assertEquals((new DateTime)->getTimestamp(), $this->requestConfig->getCurrentTimestamp(), '' , 3);
    }

    /** @test */
    function request_config_can_get_temporary_credentials_url()
    {
        $this->configStub
            ->expects($this->once())
            ->method('getTemporaryCredentialsUrl')
            ->willReturn('http://example.com');

        $this->assertEquals('http://example.com', $this->requestConfig->getTemporaryCredentialsUrl());
    }

    /** @test */
    function request_config_can_get_base_protocol_parameters()
    {
        $this->configStub
            ->expects($this->once())
            ->method('getClientCredentialsIdentifier')
            ->willReturn('client_id');

        $this->nonceGeneratorStub
            ->expects($this->once())
            ->method('generate')
            ->willReturn('random');

        $this->signerStub
            ->expects($this->once())
            ->method('getMethod')
            ->willReturn('HMAC-SHA1');

        $baseProtocolParameter = $this->requestConfig->getBaseProtocolParameters();

        $this->assertArraySubset([
            'oauth_consumer_key' => 'client_id',
            'oauth_nonce' => 'random',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_version' => '1.0',
        ], $baseProtocolParameter);

        $this->assertArrayHasKey('oauth_timestamp', $baseProtocolParameter);

        $this->assertEquals((new DateTime)->getTimestamp(), (int) $baseProtocolParameter['oauth_timestamp'], '' , 3);
    }

    /** @test */
    function request_config_can_add_signature_parameter()
    {
        $parameters = ['foo' => 'bar'];

        $this->signerStub
            ->expects($this->once())
            ->method('sign')
            ->with('http://example.com', $parameters, 'POST')
            ->willReturn('signature');

        $this->assertEquals([
            'foo' => 'bar',
            'oauth_signature' => 'signature',
        ], $this->requestConfig->addSignatureParameter($parameters, 'http://example.com', 'POST'));
    }

    /** @test */
    function request_config_can_normalize_protocol_parameter()
    {
        $this->assertEquals(
            'OAuth foo="bar"',
            $this->requestConfig->normalizeProtocolParameters(['foo' => 'bar']
        ));

        // Encode the key and value.
        $this->assertEquals(
            'OAuth foo="bar", full%20name="john%20doe"',
            $this->requestConfig->normalizeProtocolParameters([
                'foo' => 'bar',
                'full name' => 'john doe',
            ]
        ));
    }

    /** @test */
    function request_config_can_append_query_parameters_to_uri()
    {
        // The given URI without query.
        $this->assertEquals('http://example.com?name=john&age=20',
            $this->requestConfig->appendQueryParametersToUri('http://example.com', [
                'name' => 'john',
                'age' => '20',
            ])
        );

        // The given URI with query.
        $this->assertEquals('http://example.com?lang=en&name=john&age=20',
            $this->requestConfig->appendQueryParametersToUri('http://example.com?lang=en', [
                'name' => 'john',
                'age' => '20',
            ])
        );
    }

    /** @test */
    function request_config_can_get_temporary_credentials_authorization_header()
    {
        $this->requestConfigStub
            ->expects($this->once())
            ->method('getBaseProtocolParameters')
            ->willReturn(['foo' => 'bar']);

        $this->configStub
            ->expects($this->once())
            ->method('hasCallbackUri')
            ->willReturn(true);

        $this->configStub
            ->expects($this->once())
            ->method('getCallbackUri')
            ->willReturn('http://example.com/callback');

        $this->requestConfigStub
            ->expects($this->once())
            ->method('getTemporaryCredentialsUrl')
            ->willReturn('http://example.com/temporary');

        $this->requestConfigStub
            ->expects($this->once())
            ->method('addSignatureParameter')
            ->with(
                ['foo' => 'bar', 'oauth_callback' => 'http://example.com/callback'],
                'http://example.com/temporary',
                'POST'
            );

        $this->requestConfigStub
            ->expects($this->once())
            ->method('normalizeProtocolParameters')
            ->with(['foo' => 'bar', 'oauth_callback' => 'http://example.com/callback'])
            ->willReturn('Authorization Header');

        $this->assertEquals('Authorization Header', $this->requestConfigStub->getTemporaryCredentialsAuthorizationHeader());
    }

    /** @test */
    function request_config_can_build_authorization_url()
    {
        $this->configStub
            ->expects($this->once())
            ->method('getAuthorizationUrl')
            ->willReturn('http://example.com/authorize');

        $this->temporaryCredentialsStub
            ->expects($this->once())
            ->method('getIdentifier')
            ->willReturn('id_temporary');

        $this->assertEquals(
            'http://example.com/authorize?oauth_token=id_temporary',
            $this->requestConfig->buildAuthorizationUrl($this->temporaryCredentialsStub)
        );
    }
}
