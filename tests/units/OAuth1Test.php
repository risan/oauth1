<?php

use Risan\OAuth1\OAuth1;
use PHPUnit\Framework\TestCase;
use Risan\OAuth1\OAuth1Interface;
use Psr\Http\Message\StreamInterface;
use Risan\OAuth1\HttpClientInterface;
use Psr\Http\Message\ResponseInterface;
use Risan\OAuth1\Credentials\ClientCredentials;
use Risan\OAuth1\Request\RequestConfigInterface;

class OAuth1Test extends TestCase
{
    private $requestConfigStub;
    private $httpClientStub;
    private $oauth1;

    function setUp()
    {
        $this->requestConfigStub = $this->createMock(RequestConfigInterface::class);
        $this->httpClientStub = $this->createMock(HttpClientInterface::class);
        $this->oauth1 = new OAuth1($this->requestConfigStub, $this->httpClientStub);
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

        $responseStub = $this->createMock(ResponseInterface::class);
        $streamStub = $this->createMock(StreamInterface::class);

        $responseStub
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($streamStub);

        $streamStub
            ->expects($this->once())
            ->method('getContents')
            ->willReturn('oauth_token=token_id&oauth_secret=token_secret&oauth_callback_confirmed=true');

        $this->httpClientStub
            ->expects($this->once())
            ->method('post')
            ->with(
                'http://example.com/temporary_credentials_url',
                ['headers' => ['Authorization' => 'Authorization Header']]
            )
            ->willReturn($responseStub);

        $this->assertInstanceOf(ClientCredentials::class, $this->oauth1->getTemporaryCredentials());
    }
}
