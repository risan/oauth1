<?php

declare(strict_types=1);

namespace Risan\OAuth1\Test\Unit\Request;

use DateTime;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;
use Risan\OAuth1\Config\ConfigInterface;
use Risan\OAuth1\Credentials\ClientCredentials;
use Risan\OAuth1\Credentials\ServerIssuedCredentials;
use Risan\OAuth1\Credentials\TemporaryCredentials;
use Risan\OAuth1\Credentials\TokenCredentials;
use Risan\OAuth1\Request\NonceGeneratorInterface;
use Risan\OAuth1\Request\ProtocolParameter;
use Risan\OAuth1\Request\ProtocolParameterInterface;
use Risan\OAuth1\Signature\KeyBasedSignerInterface;
use Risan\OAuth1\Signature\SignerInterface;

class ProtocolParameterTest extends TestCase
{
    private $configStub;

    private $signerStub;

    private $nonceGeneratorStub;

    private $clientCredentialsStub;

    private $temporaryCredentialsStub;

    private $serverIssuedCredentialsStub;

    private $tokenCredentialsStub;

    private $psrUriStub;

    private $protocolParameter;

    protected function setUp(): void
    {
        $this->configStub = $this->createMock(ConfigInterface::class);
        $this->signerStub = $this->createMockForIntersectionOfInterfaces([
            SignerInterface::class,
            KeyBasedSignerInterface::class,
        ]);
        $this->nonceGeneratorStub = $this->createMock(NonceGeneratorInterface::class);
        $this->clientCredentialsStub = $this->createMock(ClientCredentials::class);
        $this->temporaryCredentialsStub = $this->createMock(TemporaryCredentials::class);
        $this->serverIssuedCredentialsStub = $this->createMock(ServerIssuedCredentials::class);
        $this->tokenCredentialsStub = $this->createMock(TokenCredentials::class);
        $this->psrUriStub = $this->createMock(UriInterface::class);
        $this->protocolParameter = new ProtocolParameter($this->configStub, $this->signerStub, $this->nonceGeneratorStub);
    }

    #[Test]
    public function it_implements_protocol_parameter_interface()
    {
        $this->assertInstanceOf(ProtocolParameterInterface::class, $this->protocolParameter);
    }

    #[Test]
    public function it_can_get_config()
    {
        $this->assertSame($this->configStub, $this->protocolParameter->getConfig());
    }

    #[Test]
    public function it_can_get_signer()
    {
        $this->assertSame($this->signerStub, $this->protocolParameter->getSigner());
    }

    #[Test]
    public function it_can_get_nonce_generator()
    {
        $this->assertSame($this->nonceGeneratorStub, $this->protocolParameter->getNonceGenerator());
    }

    #[Test]
    public function it_can_get_current_timestamp()
    {
        $this->assertEquals((new DateTime)->getTimestamp(), $this->protocolParameter->getCurrentTimestamp(), '', 3);
    }

    #[Test]
    public function it_can_get_version()
    {
        $this->assertEquals('1.0', $this->protocolParameter->getVersion());
    }

    #[Test]
    public function it_can_get_base()
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
            'oauth_nonce' => 'random',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '12345678',
            'oauth_version' => '1.0',
        ], $protocolParameter->getBase());
    }

    #[Test]
    public function it_can_get_for_temporary_credentials()
    {
        $callbackUri = new Uri('http://johndoe.com');
        $temporaryCredentialsUri = new Uri('http://example.com/request_token');
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
            ->method('getCallbackUri')
            ->willReturn($callbackUri);

        $this->configStub
            ->expects($this->once())
            ->method('getTemporaryCredentialsMethod')
            ->willReturn('POST');

        $this->configStub
            ->expects($this->once())
            ->method('getTemporaryCredentialsUri')
            ->willReturn($temporaryCredentialsUri);

        $protocolParameter
            ->expects($this->once())
            ->method('getSignature')
            ->with(
                ['foo' => 'bar', 'oauth_callback' => 'http://johndoe.com'],
                $temporaryCredentialsUri,
                null,
                [],
                'POST',
            )
            ->willReturn('signature');

        $this->assertSame([
            'foo' => 'bar',
            'oauth_callback' => 'http://johndoe.com',
            'oauth_signature' => 'signature',
        ], $protocolParameter->forTemporaryCredentials());
    }

    #[Test]
    public function it_can_get_for_token_credentials()
    {
        $tokenCredentialsUri = new Uri('http://example.com/access_token');
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
            ->method('getTokenCredentialsMethod')
            ->willReturn('POST');

        $this->configStub
            ->expects($this->once())
            ->method('getTokenCredentialsUri')
            ->willReturn($tokenCredentialsUri);

        $protocolParameter
            ->expects($this->once())
            ->method('getSignature')
            ->with(
                ['foo' => 'bar', 'oauth_token' => 'temporary_id'],
                $tokenCredentialsUri,
                $this->temporaryCredentialsStub,
                ['form_params' => ['oauth_verifier' => 'verification_code']],
                'POST',
            )
            ->willReturn('signature');

        $this->assertSame([
            'foo' => 'bar',
            'oauth_token' => 'temporary_id',
            'oauth_signature' => 'signature',
        ], $protocolParameter->forTokenCredentials($this->temporaryCredentialsStub, 'verification_code'));
    }

    #[Test]
    public function it_signs_a_get_token_endpoint_with_the_verifier_in_the_query()
    {
        $tokenCredentialsUri = new Uri('https://example.com/access_token');
        $protocolParameter = $this->getStub(['getBase', 'getSignature']);
        $protocolParameter->method('getBase')->willReturn(['foo' => 'bar']);
        $this->temporaryCredentialsStub->method('getIdentifier')->willReturn('temporary_id');
        $this->configStub->method('getTokenCredentialsMethod')->willReturn('GET');
        $this->configStub->method('getTokenCredentialsUri')->willReturn($tokenCredentialsUri);
        $protocolParameter
            ->expects($this->once())
            ->method('getSignature')
            ->with(
                ['foo' => 'bar', 'oauth_token' => 'temporary_id'],
                $tokenCredentialsUri,
                $this->temporaryCredentialsStub,
                ['query' => ['oauth_verifier' => 'verification_code']],
                'GET',
            )
            ->willReturn('signature');

        $this->assertSame([
            'foo' => 'bar',
            'oauth_token' => 'temporary_id',
            'oauth_signature' => 'signature',
        ], $protocolParameter->forTokenCredentials($this->temporaryCredentialsStub, 'verification_code'));
    }

    #[Test]
    public function it_can_get_for_protected_resource()
    {
        $protocolParameter = $this->getStub([
            'getBase',
            'getSignature',
        ]);

        $protocolParameter
            ->expects($this->once())
            ->method('getBase')
            ->willReturn(['foo' => 'bar']);

        $this->tokenCredentialsStub
            ->expects($this->once())
            ->method('getIdentifier')
            ->willReturn('token_id');

        $this->configStub
            ->expects($this->once())
            ->method('buildUri')
            ->with('http://example.com/protected')
            ->willReturn($this->psrUriStub);

        $protocolParameter
            ->expects($this->once())
            ->method('getSignature')
            ->with(
                ['foo' => 'bar', 'oauth_token' => 'token_id'],
                $this->psrUriStub,
                $this->tokenCredentialsStub,
                ['baz' => 'qux'],
                'GET'
            )
            ->willReturn('signature');

        $this->assertSame([
            'foo' => 'bar',
            'oauth_token' => 'token_id',
            'oauth_signature' => 'signature',
        ], $protocolParameter->forProtectedResource($this->tokenCredentialsStub, 'GET', 'http://example.com/protected', ['baz' => 'qux']));
    }

    #[Test]
    public function it_can_get_signature()
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

    #[Test]
    public function it_can_get_signature_parameters()
    {
        $this->assertEquals([
            ['foo', '1'],
            ['baz', '3'],
            ['bar', '2'],
        ], $this->protocolParameter->signatureParameters(
            ['foo' => '1'],
            [
                'query' => ['baz' => '3'],
                'form_params' => ['bar' => '2'],
            ]
        ));
    }

    #[Test]
    public function it_signs_only_form_urlencoded_raw_request_bodies()
    {
        $protocol = ['oauth_nonce' => 'nonce'];

        $this->assertSame([
            ['oauth_nonce', 'nonce'],
            ['name', 'first value'],
            ['name', 'second+value'],
            ['empty', ''],
        ], $this->protocolParameter->signatureParameters($protocol, [
            'headers' => ['content-type' => ['application/x-www-form-urlencoded; charset=UTF-8']],
            'body' => 'name=first+value&name=second%2Bvalue&empty',
        ]));

        $this->assertSame([
            ['oauth_nonce', 'nonce'],
        ], $this->protocolParameter->signatureParameters($protocol, [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => '{"name":"not-signable"}',
        ]));
    }

    #[Test]
    public function it_can_setup_signer()
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

    #[Test]
    public function it_can_check_if_signer_should_be_signed_with_client_credentials()
    {
        $this->signerStub
            ->expects($this->once())
            ->method('isKeyBased')
            ->willReturn(true);

        $this->assertTrue($this->protocolParameter->shouldSignWithClientCredentials());
    }

    #[Test]
    public function it_can_check_if_signer_should_not_be_signed_with_client_credentials()
    {
        $this->signerStub
            ->expects($this->once())
            ->method('isKeyBased')
            ->willReturn(false);

        $this->assertFalse($this->protocolParameter->shouldSignWithClientCredentials());
    }

    #[Test]
    public function it_can_check_if_signer_should_be_signed_with_server_issued_credentials()
    {
        $this->signerStub
            ->expects($this->once())
            ->method('isKeyBased')
            ->willReturn(true);

        $this->assertTrue($this->protocolParameter->shouldSignWithServerIssuedCredentials($this->serverIssuedCredentialsStub));
    }

    #[Test]
    public function it_can_check_if_signer_should_not_be_signed_with_server_issued_credentials()
    {
        $this->signerStub
            ->expects($this->once())
            ->method('isKeyBased')
            ->willReturn(true);

        $this->assertFalse($this->protocolParameter->shouldSignWithServerIssuedCredentials(null));
    }

    #[Test]
    public function it_can_check_if_request_options_has_the_given_key()
    {
        $this->assertTrue($this->protocolParameter->requestOptionsHas(['foo' => ['bar' => 'baz']], 'foo'));
        $this->assertFalse($this->protocolParameter->requestOptionsHas(['foo' => 'bar'], 'baz'));
    }

    public function getStub($methods, ?SignerInterface $signer = null)
    {
        $signer = $signer ?: $this->signerStub;

        return $this->getMockBuilder(ProtocolParameter::class)
            ->setConstructorArgs([$this->configStub, $signer, $this->nonceGeneratorStub])
            ->onlyMethods($methods)
            ->getMock();
    }
}

interface KeyBasedSigner extends KeyBasedSignerInterface, SignerInterface
{
    //
}
