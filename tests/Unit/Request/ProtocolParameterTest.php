<?php

namespace Risan\OAuth1\Test\Unit\Request;

use DateTime;
use PHPUnit\Framework\TestCase;
use Risan\OAuth1\Config\ConfigInterface;
use Risan\OAuth1\Request\ProtocolParameter;
use Risan\OAuth1\Signature\SignerInterface;
use Risan\OAuth1\Credentials\ClientCredentials;
use Risan\OAuth1\Request\NonceGeneratorInterface;
use Risan\OAuth1\Credentials\TemporaryCredentials;
use Risan\OAuth1\Signature\KeyBasedSignerInterface;
use Risan\OAuth1\Request\ProtocolParameterInterface;
use Risan\OAuth1\Credentials\ServerIssuedCredentials;

class ProtocolParameterTest extends TestCase
{
    private $configStub;
    private $signerStub;
    private $nonceGeneratorStub;
    private $clientCredentialsStub;
    private $temporaryCredentialsStub;
    private $serverIssuedCredentialsStub;
    private $protocolParameter;

    function setUp()
    {
        $this->configStub = $this->createMock(ConfigInterface::class);
        $this->signerStub = $this->createMock(SignerInterface::class);
        $this->nonceGeneratorStub = $this->createMock(NonceGeneratorInterface::class);
        $this->clientCredentialsStub = $this->createMock(ClientCredentials::class);
        $this->temporaryCredentialsStub = $this->createMock(TemporaryCredentials::class);
        $this->serverIssuedCredentialsStub = $this->createMock(ServerIssuedCredentials::class);
        $this->protocolParameter = new ProtocolParameter($this->configStub, $this->signerStub, $this->nonceGeneratorStub);
    }

    /** @test */
    function it_implements_protocol_parameter_interface()
    {
        $this->assertInstanceOf(ProtocolParameterInterface::class, $this->protocolParameter);
    }

    /** @test */
    function it_can_get_config()
    {
        $this->assertSame($this->configStub, $this->protocolParameter->getConfig());
    }

    /** @test */
    function it_can_get_signer()
    {
        $this->assertSame($this->signerStub, $this->protocolParameter->getSigner());
    }

    /** @test */
    function it_can_get_nonce_generator()
    {
        $this->assertSame($this->nonceGeneratorStub, $this->protocolParameter->getNonceGenerator());
    }

    /** @test */
    function it_can_get_current_timestamp()
    {
        $this->assertEquals((new DateTime)->getTimestamp(), $this->protocolParameter->getCurrentTimestamp(), '' , 3);
    }

    /** @test */
    function it_can_get_version()
    {
        $this->assertEquals('1.0', $this->protocolParameter->getVersion());
    }

    /** @test */
    function it_can_get_base()
    {
        $protocolParameter = $this->getStub(['getCurrentTimestamp']);

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

        $protocolParameter
            ->expects($this->once())
            ->method('getCurrentTimestamp')
            ->willReturn(12345678);

        $this->assertSame([
            'oauth_consumer_key' => 'client_id',
            'oauth_nonce' =>'random',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '12345678',
            'oauth_version' => '1.0',
        ], $protocolParameter->getBase());
    }

    /** @test */
    function it_can_get_for_temporary_credentials()
    {
        $protocolParameter = $this->getStub([
            'getBase',
            'getSignature',
        ]);

        $protocolParameter
            ->expects($this->once())
            ->method('getBase')
            ->willReturn(['foo' => 'bar']);

        $this->configStub
            ->expects($this->once())
            ->method('hasCallbackUri')
            ->willReturn(true);

        $this->configStub
            ->expects($this->once())
            ->method('getCallbackUri')
            ->willReturn('http://johndoe.com');

        $this->configStub
            ->expects($this->once())
            ->method('getTemporaryCredentialsUri')
            ->willReturn('http://example.com/request_token');

        $protocolParameter
            ->expects($this->once())
            ->method('getSignature')
            ->with(
                ['foo' => 'bar', 'oauth_callback' => 'http://johndoe.com'],
                'http://example.com/request_token'
            )
            ->willReturn('signature');

        $this->assertSame([
            'foo' => 'bar',
            'oauth_callback' => 'http://johndoe.com',
            'oauth_signature' => 'signature',
        ], $protocolParameter->forTemporaryCredentials());
    }

    /** @test */
    function it_can_get_for_token_credentials()
    {
        $protocolParameter = $this->getStub([
            'getBase',
            'getSignature',
        ]);

        $protocolParameter
            ->expects($this->once())
            ->method('getBase')
            ->willReturn(['foo' => 'bar']);

        $this->temporaryCredentialsStub
            ->expects($this->once())
            ->method('getIdentifier')
            ->willReturn('temporary_id');

        $this->configStub
            ->expects($this->once())
            ->method('getTokenCredentialsUri')
            ->willReturn('http://example.com/access_token');

        $protocolParameter
            ->expects($this->once())
            ->method('getSignature')
            ->with(
                ['foo' => 'bar', 'oauth_token' => 'temporary_id'],
                'http://example.com/access_token',
                $this->temporaryCredentialsStub,
                ['form_params' => ['oauth_verifier' => 'verification_code']]
            )
            ->willReturn('signature');

        $this->assertSame([
            'foo' => 'bar',
            'oauth_token' => 'temporary_id',
            'oauth_signature' => 'signature',
        ], $protocolParameter->forTokenCredentials($this->temporaryCredentialsStub, 'verification_code'));
    }

    /** @test */
    function it_can_get_signature()
    {
        $protocolParameter = $this->getStub(['signatureParameters', 'setupSigner']);

        $protocolParameter
            ->expects($this->once())
            ->method('signatureParameters')
            ->with(['foo' => 'bar'], ['baz' => 'qux'])
            ->willReturn(['foo' => 'bar', 'baz' => 'qux']);

        $protocolParameter
            ->expects($this->once())
            ->method('setupSigner')
            ->willReturn($this->signerStub);

        $this->signerStub
            ->expects($this->once())
            ->method('sign')
            ->with(
                'http://example.com',
                ['foo' => 'bar', 'baz' => 'qux'],
                'POST'
            )
            ->willReturn('signature');

        $this->assertEquals('signature', $protocolParameter->getSignature(
            ['foo' => 'bar'],
            'http://example.com',
            null,
            ['baz' => 'qux']
        ));
    }

    /** @test */
    function it_can_get_signature_parameters()
    {
        $this->assertEquals([
            'foo' => '1',
            'bar' => '2',
            'baz' => '3',
        ], $this->protocolParameter->signatureParameters(
            ['foo' => '1'],
            [
                'query' => ['baz' => '3'],
                'form_params' => ['bar' => '2'],
            ]
        ));
    }

    /** @test */
    function it_can_setup_signer()
    {
        $signerStub = $this->createMock(KeyBasedSigner::class);

        $protocolParameter = $this->getStub([
            'shouldSignWithClientCredentials',
            'shouldSignWithServerIssuedCredentials',
        ], $signerStub);

        $protocolParameter
            ->expects($this->once())
            ->method('shouldSignWithClientCredentials')
            ->willReturn(true);

        $protocolParameter
            ->expects($this->once())
            ->method('shouldSignWithServerIssuedCredentials')
            ->willReturn(true);

        $this->configStub
            ->expects($this->once())
            ->method('getClientCredentials')
            ->willReturn($this->clientCredentialsStub);

        $signerStub
            ->expects($this->once())
            ->method('setClientCredentials')
            ->with($this->clientCredentialsStub);

        $signerStub
            ->expects($this->once())
            ->method('setServerIssuedCredentials')
            ->with($this->serverIssuedCredentialsStub);

        $this->assertSame($signerStub, $protocolParameter->setupSigner($this->serverIssuedCredentialsStub));
    }

     /** @test */
    function it_can_check_if_signer_should_be_signed_with_client_credentials()
    {
        $this->signerStub
            ->expects($this->once())
            ->method('isKeyBased')
            ->willReturn(true);

        $this->assertTrue($this->protocolParameter->shouldSignWithClientCredentials());
    }

     /** @test */
    function it_can_check_if_signer_should_not_be_signed_with_client_credentials()
    {
        $this->signerStub
            ->expects($this->once())
            ->method('isKeyBased')
            ->willReturn(false);

        $this->assertFalse($this->protocolParameter->shouldSignWithClientCredentials());
    }

    /** @test */
    function it_can_check_if_signer_should_be_signed_with_server_issued_credentials()
    {
        $this->signerStub
            ->expects($this->once())
            ->method('isKeyBased')
            ->willReturn(true);

        $this->assertTrue($this->protocolParameter->shouldSignWithServerIssuedCredentials($this->serverIssuedCredentialsStub));
    }

    /** @test */
    function it_can_check_if_signer_should_not_be_signed_with_server_issued_credentials()
    {
        $this->signerStub
            ->expects($this->once())
            ->method('isKeyBased')
            ->willReturn(true);

        $this->assertFalse($this->protocolParameter->shouldSignWithServerIssuedCredentials(null));
    }

    /** @test */
    function it_can_check_if_request_options_has_the_given_key()
    {
        $this->assertTrue($this->protocolParameter->requestOptionsHas(['foo' => ['bar' => 'baz']], 'foo'));
        $this->assertFalse($this->protocolParameter->requestOptionsHas(['foo' => 'bar'], 'baz'));
    }

    function getStub($methods, SignerInterface $signer = null)
    {
        $signer = $signer ?: $this->signerStub;

        return $this->getMockBuilder(ProtocolParameter::class)
            ->setConstructorArgs([$this->configStub, $signer, $this->nonceGeneratorStub])
            ->setMethods($methods)
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
    }
}

interface KeyBasedSigner extends SignerInterface, KeyBasedSignerInterface {
    //
}
