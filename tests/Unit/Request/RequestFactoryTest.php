<?php

namespace Risan\OAuth1\Test\Unit\Request;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;
use Risan\OAuth1\Config\ConfigInterface;
use Risan\OAuth1\Request\RequestFactory;
use Risan\OAuth1\Request\RequestInterface;
use Risan\OAuth1\Request\UriParserInterface;
use Risan\OAuth1\Request\RequestFactoryInterface;
use Risan\OAuth1\Credentials\TemporaryCredentials;
use Risan\OAuth1\Request\AuthorizationHeaderInterface;

class RequestFactoryTest extends TestCase
{
    private $authorizationHeaderStub;
    private $uriParserStub;
    private $configStub;
    private $requestFactory;
    private $requestFactoryStub;
    private $requestStub;
    private $temporaryCredentialsStub;
    private $psrUriStub;

    function setUp()
    {
        $this->authorizationHeaderStub = $this->createMock(AuthorizationHeaderInterface::class);
        $this->uriParserStub = $this->createMock(UriParserInterface::class);
        $this->configStub = $this->createMock(ConfigInterface::class);
        $this->requestFactory = new RequestFactory($this->authorizationHeaderStub, $this->uriParserStub);
        $this->requestStub = $this->createMock(RequestInterface::class);
        $this->temporaryCredentialsStub = $this->createMock(TemporaryCredentials::class);
        $this->psrUriStub = $this->createMock(UriInterface::class);

        $this->requestFactoryStub = $this->getMockBuilder(RequestFactory::class)
            ->setConstructorArgs([$this->authorizationHeaderStub, $this->uriParserStub])
            ->setMethods(['getConfig', 'create'])
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
    }

    /** @test */
    function it_implements_request_factory_interface()
    {
        $this->assertInstanceOf(RequestFactoryInterface::class, $this->requestFactory);
    }

    /** @test */
    function it_can_get_authorization_header()
    {
        $this->assertSame($this->authorizationHeaderStub, $this->requestFactory->getAuthorizationHeader());
    }

    /** @test */
    function it_can_get_uri_parser()
    {
        $this->assertSame($this->uriParserStub, $this->requestFactory->getUriParser());
    }

    /** @test */
    function it_can_get_config()
    {
        $this->authorizationHeaderStub
            ->expects($this->once())
            ->method('getConfig')
            ->willReturn($this->configStub);

        $this->assertSame($this->configStub, $this->requestFactory->getConfig());
    }

    /** @test */
    function it_can_create_for_temporary_credentials()
    {
        $this->requestFactoryStub
            ->expects($this->once())
            ->method('getConfig')
            ->willReturn($this->configStub);

        $this->configStub
            ->expects($this->once())
            ->method('getTemporaryCredentialsUri')
            ->willReturn($this->psrUriStub);

        $this->psrUriStub
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('http://example.com');

        $this->authorizationHeaderStub
            ->expects($this->once())
            ->method('forTemporaryCredentials')
            ->willReturn('OAuth1');

        $this->requestFactoryStub
            ->expects($this->once())
            ->method('create')
            ->with('POST', 'http://example.com', [
                'headers' => ['Authorization' => 'OAuth1'],
            ])
            ->willReturn($this->requestStub);

        $this->assertSame($this->requestStub, $this->requestFactoryStub->createForTemporaryCredentials());
    }

    /** @test */
    function it_can_build_authorization_uri()
    {
        $this->requestFactoryStub
            ->expects($this->once())
            ->method('getConfig')
            ->willReturn($this->configStub);

        $this->configStub
            ->expects($this->once())
            ->method('getAuthorizationUri')
            ->willReturn($this->psrUriStub);

        $this->temporaryCredentialsStub
            ->expects($this->once())
            ->method('getIdentifier')
            ->willReturn('temporary_id');

        $this->uriParserStub
            ->expects($this->once())
            ->method('appendQueryParameters')
            ->with($this->psrUriStub, ['oauth_token' => 'temporary_id'])
            ->willReturn($this->psrUriStub);

        $this->assertSame(
            $this->psrUriStub,
            $this->requestFactoryStub->buildAuthorizationUri($this->temporaryCredentialsStub)
        );
    }

    /** @test */
    function it_can_create_for_token_credentials()
    {
        $this->requestFactoryStub
            ->expects($this->once())
            ->method('getConfig')
            ->willReturn($this->configStub);

        $this->configStub
            ->expects($this->once())
            ->method('getTokenCredentialsUri')
            ->willReturn($this->psrUriStub);

        $this->psrUriStub
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('http://example.com');

        $this->authorizationHeaderStub
            ->expects($this->once())
            ->method('forTokenCredentials')
            ->with($this->temporaryCredentialsStub, 'verification_code')
            ->willReturn('OAuth1');

        $this->requestFactoryStub
            ->expects($this->once())
            ->method('create')
            ->with('POST', 'http://example.com', [
                'headers' => ['Authorization' => 'OAuth1'],
                'form_params' => ['oauth_verifier' => 'verification_code'],
            ])
            ->willReturn($this->requestStub);

        $this->assertSame(
            $this->requestStub,
            $this->requestFactoryStub->createForTokenCredentials($this->temporaryCredentialsStub, 'verification_code')
        );
    }
}
