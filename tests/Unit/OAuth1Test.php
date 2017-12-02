<?php

namespace Risan\OAuth1\Test\Unit;

use Risan\OAuth1\OAuth1;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Risan\OAuth1\OAuth1Interface;
use Psr\Http\Message\UriInterface;
use Risan\OAuth1\HttpClientInterface;
use Psr\Http\Message\ResponseInterface;
use Risan\OAuth1\Config\ConfigInterface;
use Risan\OAuth1\Request\RequestInterface;
use Risan\OAuth1\Credentials\TokenCredentials;
use Risan\OAuth1\Request\RequestFactoryInterface;
use Risan\OAuth1\Credentials\CredentialsException;
use Risan\OAuth1\Credentials\TemporaryCredentials;
use Risan\OAuth1\Credentials\CredentialsFactoryInterface;

class OAuth1Test extends TestCase
{
    private $httpClientStub;
    private $requestFactoryStub;
    private $configStub;
    private $credentialsFactoryStub;
    private $temporaryCredentialsStub;
    private $tokenCredentialsStub;
    private $oauth1;
    private $requestStub;
    private $responseStub;
    private $psrUriStub;

    function setUp()
    {
        $this->httpClientStub = $this->createMock(HttpClientInterface::class);
        $this->requestFactoryStub = $this->createMock(RequestFactoryInterface::class);
        $this->configStub = $this->createMock(ConfigInterface::class);
        $this->credentialsFactoryStub = $this->createMock(CredentialsFactoryInterface::class);
        $this->temporaryCredentialsStub = $this->createMock(TemporaryCredentials::class);
        $this->tokenCredentialsStub = $this->createMock(TokenCredentials::class);
        $this->requestStub = $this->createMock(RequestInterface::class);
        $this->responseStub = $this->createMock(ResponseInterface::class);
        $this->psrUriStub = $this->createMock(UriInterface::class);
        $this->oauth1 = new OAuth1($this->httpClientStub, $this->requestFactoryStub, $this->credentialsFactoryStub);
    }

    /** @test */
    function it_implements_oauth1_interface()
    {
        $this->assertInstanceOf(OAuth1Interface::class, $this->oauth1);
    }

    /** @test */
    function it_can_get_http_client()
    {
        $this->assertSame($this->httpClientStub, $this->oauth1->getHttpClient());
    }

    /** @test */
    function it_can_get_request_factory()
    {
        $this->assertSame($this->requestFactoryStub, $this->oauth1->getRequestFactory());
    }

    /** @test */
    function it_can_get_credentials_factory()
    {
        $this->assertSame($this->credentialsFactoryStub, $this->oauth1->getCredentialsFactory());
    }

    /** @test */
    function it_can_get_config()
    {
        $this->requestFactoryStub
            ->expects($this->once())
            ->method('getConfig')
            ->willReturn($this->configStub);

        $this->assertSame($this->configStub, $this->oauth1->getConfig());
    }

    /** @test */
    function it_can_get_and_set_token_credentials()
    {
        $this->assertNull($this->oauth1->getTokenCredentials());

        $this->assertSame($this->oauth1, $this->oauth1->setTokenCredentials($this->tokenCredentialsStub));

        $this->assertSame($this->tokenCredentialsStub, $this->oauth1->getTokenCredentials());
    }

    /** @test */
    function it_can_request_for_temporary_credentials()
    {
        $this->requestFactoryStub
            ->expects($this->once())
            ->method('createForTemporaryCredentials')
            ->willReturn($this->requestStub);

        $this->httpClientStub
            ->expects($this->once())
            ->method('send')
            ->with($this->requestStub)
            ->willReturn($this->responseStub);

        $this->credentialsFactoryStub
            ->expects($this->once())
            ->method('createTemporaryCredentialsFromResponse')
            ->with($this->responseStub)
            ->willReturn($this->temporaryCredentialsStub);

        $this->assertSame($this->temporaryCredentialsStub, $this->oauth1->requestTemporaryCredentials());
    }

    /** @test */
    function it_can_build_authorization_uri()
    {
        $this->requestFactoryStub
            ->expects($this->once())
            ->method('buildAuthorizationUri')
            ->with($this->temporaryCredentialsStub)
            ->willReturn($this->psrUriStub);

        $this->psrUriStub
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('http://example.com');

        $this->assertEquals(
            'http://example.com',
            $this->oauth1->buildAuthorizationUri($this->temporaryCredentialsStub)
        );
    }

    /** @test */
    function it_throws_exception_when_requesting_token_credentials_but_temporary_credentials_identifier_does_not_match()
    {
        $this->temporaryCredentialsStub
            ->expects($this->once())
            ->method('getIdentifier')
            ->willReturn('invalid');

        $this->expectException(InvalidArgumentException::class);

        $this->oauth1->requestTokenCredentials($this->temporaryCredentialsStub, 'temporary_id', 'verification_code');
    }

    /** @test */
    function it_can_request_for_token_credentials()
    {
        $this->temporaryCredentialsStub
            ->expects($this->once())
            ->method('getIdentifier')
            ->willReturn('temporary_id');

        $this->requestFactoryStub
            ->expects($this->once())
            ->method('createForTokenCredentials')
            ->with($this->temporaryCredentialsStub, 'verification_code')
            ->willReturn($this->requestStub);

        $this->httpClientStub
            ->expects($this->once())
            ->method('send')
            ->with($this->requestStub)
            ->willReturn($this->responseStub);

        $this->credentialsFactoryStub
            ->expects($this->once())
            ->method('createTokenCredentialsFromResponse')
            ->with($this->responseStub)
            ->willReturn($this->tokenCredentialsStub);

        $this->assertSame(
            $this->tokenCredentialsStub,
            $this->oauth1->requestTokenCredentials($this->temporaryCredentialsStub, 'temporary_id', 'verification_code')
        );
    }

    /** @test */
    function it_throws_exception_if_token_credential_is_not_set()
    {
        $this->expectException(CredentialsException::class);
        $this->oauth1->request('GET', 'http://example.com', ['foo' => 'bar']);
    }

    /** @test */
    function it_can_request_for_protected_resource()
    {
        $this->oauth1->setTokenCredentials($this->tokenCredentialsStub);

        $this->requestFactoryStub
            ->expects($this->once())
            ->method('createForProtectedResource')
            ->with($this->tokenCredentialsStub, 'GET', 'http://example.com', ['foo' => 'bar'])
            ->willReturn($this->requestStub);

        $this->httpClientStub
            ->expects($this->once())
            ->method('send')
            ->with($this->requestStub)
            ->willReturn($this->responseStub);

        $this->assertSame(
            $this->responseStub,
            $this->oauth1->request('GET', 'http://example.com', ['foo' => 'bar'])
        );
    }

    /** @test */
    function it_can_send_get_request()
    {
        $oauth1 = $this->getStubWithRequestMethod();

        $oauth1
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'http://example.com', ['foo' => 'bar'])
            ->willReturn($this->responseStub);
        
        $this->assertSame(
            $this->responseStub,
            $oauth1->get('http://example.com', ['foo' => 'bar'])
        );
    }

    /** @test */
    function it_can_send_post_request()
    {
        $oauth1 = $this->getStubWithRequestMethod();

        $oauth1
            ->expects($this->once())
            ->method('request')
            ->with('POST', 'http://example.com', ['foo' => 'bar'])
            ->willReturn($this->responseStub);
        
        $this->assertSame(
            $this->responseStub,
            $oauth1->post('http://example.com', ['foo' => 'bar'])
        );
    }

    /** @test */
    function it_can_send_put_request()
    {
        $oauth1 = $this->getStubWithRequestMethod();

        $oauth1
            ->expects($this->once())
            ->method('request')
            ->with('PUT', 'http://example.com', ['foo' => 'bar'])
            ->willReturn($this->responseStub);
        
        $this->assertSame(
            $this->responseStub,
            $oauth1->put('http://example.com', ['foo' => 'bar'])
        );
    }

    /** @test */
    function it_can_send_patch_request()
    {
        $oauth1 = $this->getStubWithRequestMethod();

        $oauth1
            ->expects($this->once())
            ->method('request')
            ->with('PATCH', 'http://example.com', ['foo' => 'bar'])
            ->willReturn($this->responseStub);
        
        $this->assertSame(
            $this->responseStub,
            $oauth1->patch('http://example.com', ['foo' => 'bar'])
        );
    }

    function getStubWithRequestMethod()
    {
        return $this->getMockBuilder(OAuth1::class)
            ->setConstructorArgs([$this->httpClientStub, $this->requestFactoryStub, $this->credentialsFactoryStub])
            ->setMethods(['request'])
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
    }
}
