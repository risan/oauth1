<?php

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\ConfigInterface;
use Risan\OAuth1\Request\RequestBuilder;
use Risan\OAuth1\Signature\SignerInterface;
use Risan\OAuth1\Request\NonceGeneratorInterface;
use Risan\OAuth1\Request\RequestBuilderInterface;

class RequestBuilderTest extends TestCase
{
    private $configStub;
    private $signerStub;
    private $nonceGeneratorStub;
    private $requestBuilder;
    private $requestBuilderStub;

    function setUp()
    {
        $this->configStub = $this->createMock(ConfigInterface::class);
        $this->signerStub = $this->createMock(SignerInterface::class);
        $this->nonceGeneratorStub = $this->createMock(NonceGeneratorInterface::class);
        $this->requestBuilder = new RequestBuilder($this->configStub, $this->signerStub, $this->nonceGeneratorStub);

        $this->requestBuilderStub = $this->getMockBuilder(RequestBuilder::class)
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
    function request_builder_is_an_instance_of_request_builder_interface()
    {
        $this->assertInstanceOf(RequestBuilderInterface::class, $this->requestBuilder);
    }

    /** @test */
    function request_builder_can_get_config()
    {
        $this->assertSame($this->configStub, $this->requestBuilder->getConfig());
    }

    /** @test */
    function request_builder_can_get_signer()
    {
        $this->assertSame($this->signerStub, $this->requestBuilder->getSigner());
    }

    /** @test */
    function request_builder_can_get_nonce_generator()
    {
        $this->assertSame($this->nonceGeneratorStub, $this->requestBuilder->getNonceGenerator());
    }

    /** @test */
    function request_builder_can_get_current_timestamp()
    {
        $this->assertEquals((new DateTime)->getTimestamp(), $this->requestBuilder->getCurrentTimestamp(), '' , 3);
    }

    /** @test */
    function request_builder_can_get_temporary_credentials_url()
    {
        $this->configStub
            ->expects($this->once())
            ->method('getTemporaryCredentialsUrl')
            ->willReturn('http://example.com');

        $this->assertEquals('http://example.com', $this->requestBuilder->getTemporaryCredentialsUrl());
    }

    /** @test */
    function request_builder_can_get_base_protocol_parameters()
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

        $baseProtocolParameter = $this->requestBuilder->getBaseProtocolParameters();

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
    function request_builder_can_add_signature_parameter()
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
        ], $this->requestBuilder->addSignatureParameter($parameters, 'http://example.com', 'POST'));
    }

    /** @test */
    function request_builder_can_normalize_protocol_parameter()
    {
        $this->assertEquals(
            'OAuth foo="bar"',
            $this->requestBuilder->normalizeProtocolParameters(['foo' => 'bar']
        ));

        // Encode the key and value.
        $this->assertEquals(
            'OAuth foo="bar", full%20name="john%20doe"',
            $this->requestBuilder->normalizeProtocolParameters([
                'foo' => 'bar',
                'full name' => 'john doe',
            ]
        ));
    }

    /** @test */
    function request_builder_can_get_temporary_credentials_authorization_header()
    {
        $this->requestBuilderStub
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

        $this->requestBuilderStub
            ->expects($this->once())
            ->method('getTemporaryCredentialsUrl')
            ->willReturn('http://example.com/temporary');

        $this->requestBuilderStub
            ->expects($this->once())
            ->method('addSignatureParameter')
            ->with(
                ['foo' => 'bar', 'oauth_callback' => 'http://example.com/callback'],
                'http://example.com/temporary',
                'POST'
            );

        $this->requestBuilderStub
            ->expects($this->once())
            ->method('normalizeProtocolParameters')
            ->with(['foo' => 'bar', 'oauth_callback' => 'http://example.com/callback'])
            ->willReturn('Authorization Header');

        $this->assertEquals('Authorization Header', $this->requestBuilderStub->getTemporaryCredentialsAuthorizationHeader());
    }
}
