<?php

use PHPUnit\Framework\TestCase;
use Risan\OAuth1\ConfigInterface;
use Risan\OAuth1\Request\RequestConfig;
use Risan\OAuth1\Signature\HmacSha1Signer;
use Risan\OAuth1\Signature\SignerInterface;
use Risan\OAuth1\Credentials\ClientCredentials;
use Risan\OAuth1\Request\RequestConfigInterface;
use Risan\OAuth1\Request\NonceGeneratorInterface;
use Risan\OAuth1\Credentials\TemporaryCredentials;
use Risan\OAuth1\Credentials\ServerIssuedCredentials;

class RequestConfigTest extends TestCase
{
    private $configStub;
    private $signerStub;
    private $hmacSha1SignerStub;
    private $nonceGeneratorStub;
    private $requestConfig;
    private $clientCredentialsStub;
    private $temporaryCredentialsStub;
    private $serverIssuedCredentialsStub;
    private $requestConfigStub;

    function setUp()
    {
        $this->configStub = $this->createMock(ConfigInterface::class);
        $this->signerStub = $this->createMock(SignerInterface::class);
        $this->hmacSha1SignerStub = $this->createMock(HmacSha1Signer::class);
        $this->nonceGeneratorStub = $this->createMock(NonceGeneratorInterface::class);
        $this->requestConfig = new RequestConfig($this->configStub, $this->signerStub, $this->nonceGeneratorStub);
        $this->clientCredentialsStub = $this->createMock(ClientCredentials::class);
        $this->temporaryCredentialsStub = $this->createMock(TemporaryCredentials::class);
        $this->serverIssuedCredentialsStub = $this->createMock(ServerIssuedCredentials::class);

        $this->requestConfigStub = $this->getMockBuilder(RequestConfig::class)
            ->setConstructorArgs([$this->configStub, $this->signerStub, $this->nonceGeneratorStub])
            ->setMethods([
                'getBaseProtocolParameters',
                'getTemporaryCredentialsUrl',
                'getTokenCredentialsUrl',
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
    function request_config_can_check_if_signer_is_key_based_or_not()
    {
        $this->assertFalse($this->requestConfig->isUsingKeyBasedSigner());

        // With keybased signer.
        $this->configStub
            ->expects($this->once())
            ->method('getClientCredentials')
            ->willReturn($this->clientCredentialsStub);

        $requestConfig = new RequestConfig($this->configStub, $this->createMock(HmacSha1Signer::class), $this->nonceGeneratorStub);
        $this->assertTrue($requestConfig->isUsingKeyBasedSigner());
    }

    /** @test */
    function request_config_can_get_temporary_credentials_url()
    {
        $this->configStub
            ->expects($this->once())
            ->method('getTemporaryCredentialsUrl')
            ->willReturn('http://example.com/request_token');

        $this->assertEquals('http://example.com/request_token', $this->requestConfig->getTemporaryCredentialsUrl());
    }

    /** @test */
    function request_config_can_get_token_credentials_url()
    {
        $this->configStub
            ->expects($this->once())
            ->method('getTokenCredentialsUrl')
            ->willReturn('http://example.com/access_token');

        $this->assertEquals('http://example.com/access_token', $this->requestConfig->getTokenCredentialsUrl());
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
        $parameters = ['oauth_consumer_key' => 'client_id'];

        $this->signerStub
            ->expects($this->once())
            ->method('sign')
            ->with(
                'http://example.com',
                array_merge($parameters, [
                    'name' => 'john',
                    'age' => 20,
                ]),
                'POST'
            )
            ->willReturn('signature');

        $this->assertEquals([
            'oauth_consumer_key' => 'client_id',
            'oauth_signature' => 'signature',
        ], $this->requestConfig->addSignatureParameter(
            $parameters,
            'http://example.com',
            null,
            [
                'form_params' => ['name' => 'john'],
                'query' => ['age' => 20],
            ],
            'POST'
        ));
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
    function request_config_can_check_request_options_key()
    {
        // Key not found.
        $this->assertFalse(
            $this->requestConfig->requestOptionsHas([], 'form_params'
        ));

        // Not an array.
        $this->assertFalse(
            $this->requestConfig->requestOptionsHas(['form_params' => 'foo bar'], 'form_params'
        ));

        // Empty array.
        $this->assertFalse(
            $this->requestConfig->requestOptionsHas(['form_params' => []], 'form_params'
        ));

        $this->assertTrue(
            $this->requestConfig->requestOptionsHas(['form_params' => ['foo' => 'bar']], 'form_params'
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
            ->willReturn(['oauth_consumer_key' => 'client_id']);

        $this->configStub
            ->expects($this->once())
            ->method('hasCallbackUri')
            ->willReturn(true);

        $this->configStub
            ->expects($this->once())
            ->method('getCallbackUri')
            ->willReturn('http://johndoe.com/callback');

        $this->requestConfigStub
            ->expects($this->once())
            ->method('getTemporaryCredentialsUrl')
            ->willReturn('http://example.com/request_token');

        $this->requestConfigStub
            ->expects($this->once())
            ->method('addSignatureParameter')
            ->with(
                [
                    'oauth_consumer_key' => 'client_id',
                    'oauth_callback' => 'http://johndoe.com/callback',
                ],
                'http://example.com/request_token'
            );

        $this->requestConfigStub
            ->expects($this->once())
            ->method('normalizeProtocolParameters')
            ->with([
                'oauth_consumer_key' => 'client_id',
                'oauth_callback' => 'http://johndoe.com/callback',
            ])
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

    /** @test */
    function request_config_can_get_token_credentials_authorization_header()
    {
        $this->requestConfigStub
            ->expects($this->once())
            ->method('getBaseProtocolParameters')
            ->willReturn(['oauth_consumer_key' => 'client_id']);

        $this->temporaryCredentialsStub
            ->expects($this->once())
            ->method('getIdentifier')
            ->willReturn('temporary_id');

        $this->requestConfigStub
            ->expects($this->once())
            ->method('getTokenCredentialsUrl')
            ->willReturn('http://example.com/access_token');

        $this->requestConfigStub
            ->expects($this->once())
            ->method('addSignatureParameter')
            ->with(
                [
                    'oauth_consumer_key' => 'client_id',
                    'oauth_token' => 'temporary_id',
                ],
                'http://example.com/access_token',
                $this->temporaryCredentialsStub,
                [
                    'form_params' => [
                        'oauth_verifier' => 'verification_code',
                    ],
                ],
                'POST'
            );

        $this->requestConfigStub
            ->expects($this->once())
            ->method('normalizeProtocolParameters')
            ->with([
                'oauth_consumer_key' => 'client_id',
                'oauth_token' => 'temporary_id',
            ])
            ->willReturn('Authorization Header');

        $this->assertEquals(
            'Authorization Header',
            $this->requestConfigStub->getTokenCredentialsAuthorizationHeader(
                $this->temporaryCredentialsStub,
                'verification_code'
            )
        );
    }
}
