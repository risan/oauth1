<?php

use Risan\OAuth1\OAuth1;
use PHPUnit\Framework\TestCase;
use Risan\OAuth1\OAuth1Interface;
use Risan\OAuth1\HttpClientInterface;
use Psr\Http\Message\ResponseInterface;
use Risan\OAuth1\Request\RequestConfigInterface;
use Risan\OAuth1\Credentials\TemporaryCredentials;
use Risan\OAuth1\Credentials\CredentialsFactoryInterface;

class OAuth1Test extends TestCase
{
    private $requestConfigStub;
    private $httpClientStub;
    private $credentialsFactoryStub;
    private $oauth1;
    private $responseStub;

    function setUp()
    {
        $this->requestConfigStub = $this->createMock(RequestConfigInterface::class);
        $this->httpClientStub = $this->createMock(HttpClientInterface::class);
        $this->credentialsFactoryStub = $this->createMock(CredentialsFactoryInterface::class);
        $this->oauth1 = new OAuth1($this->requestConfigStub, $this->httpClientStub, $this->credentialsFactoryStub);
        $this->responseStub = $this->createMock(ResponseInterface::class);
    }

    /** @test */
    function oauth1_is_an_instance_of_oauth1_interface()
    {
        $this->assertInstanceOf(OAuth1Interface::class, $this->oauth1);
    }

    /** @test */
    function oauth1_can_get_request_config()
    {
        $this->assertInstanceOf(RequestConfigInterface::class, $this->oauth1->getRequestConfig());
    }

    /** @test */
    function oauth1_can_get_http_client()
    {
        $this->assertInstanceOf(HttpClientInterface::class, $this->oauth1->getHttpClient());
    }

    /** @test */
    function oauth1_can_obtain_temporary_credentials()
    {
        $this->requestConfigStub
            ->expects($this->once())
            ->method('getTemporaryCredentialsUrl')
            ->willReturn('http://example.com/temporary_credentials_url');

        $this->requestConfigStub
            ->expects($this->once())
            ->method('getTemporaryCredentialsAuthorizationHeader')
            ->willReturn('Authorization Header');

        $this->httpClientStub
            ->expects($this->once())
            ->method('post')
            ->with(
                'http://example.com/temporary_credentials_url',
                ['headers' => ['Authorization' => 'Authorization Header']]
            )
            ->willReturn($this->responseStub);

        $temporaryCredentialsStub = $this->createMock(TemporaryCredentials::class);

        $this->credentialsFactoryStub
            ->expects($this->once())
            ->method('createTemporaryCredentialsFromResponse')
            ->with($this->responseStub)
            ->willReturn($temporaryCredentialsStub);

        $this->assertInstanceOf(TemporaryCredentials::class, $this->oauth1->getTemporaryCredentials());
    }
}
