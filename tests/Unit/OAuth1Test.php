<?php

namespace Risan\OAuth1\Test\Unit;

use Risan\OAuth1\OAuth1;
use PHPUnit\Framework\TestCase;
use Risan\OAuth1\OAuth1Interface;
use Risan\OAuth1\HttpClientInterface;
use Psr\Http\Message\ResponseInterface;
use Risan\OAuth1\Request\RequestInterface;
use Risan\OAuth1\Request\RequestFactoryInterface;
use Risan\OAuth1\Credentials\TemporaryCredentials;
use Risan\OAuth1\Credentials\CredentialsFactoryInterface;

class OAuth1Test extends TestCase
{
    private $httpClientStub;
    private $requestFactoryStub;
    private $credentialsFactoryStub;
    private $temporaryCredentialsStub;
    private $oauth1;
    private $requestStub;
    private $responseStub;

    function setUp()
    {
        $this->httpClientStub = $this->createMock(HttpClientInterface::class);
        $this->requestFactoryStub = $this->createMock(RequestFactoryInterface::class);
        $this->credentialsFactoryStub = $this->createMock(CredentialsFactoryInterface::class);
        $this->temporaryCredentialsStub = $this->createMock(TemporaryCredentials::class);
        $this->requestStub = $this->createMock(RequestInterface::class);
        $this->responseStub = $this->createMock(ResponseInterface::class);
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
    function it_can_obtain_temporary_credentials()
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

        $this->assertSame($this->temporaryCredentialsStub, $this->oauth1->getTemporaryCredentials());
    }
}
