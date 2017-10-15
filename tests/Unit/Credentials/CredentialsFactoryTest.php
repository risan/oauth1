<?php

namespace Risan\OAuth1\Test\Unit\Credentials;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;
use Risan\OAuth1\Credentials\CredentialsFactory;
use Risan\OAuth1\Credentials\CredentialsException;
use Risan\OAuth1\Credentials\TemporaryCredentials;

class CredentialsFactoryTest extends TestCase
{
    private $credentialsFactory;
    private $responseStub;
    private $streamStub;

    function setUp()
    {
        $this->credentialsFactory = new CredentialsFactory;
        $this->responseStub = $this->createMock(ResponseInterface::class);
        $this->streamStub = $this->createMock(StreamInterface::class);
    }

    /** @test */
    function it_throws_credentials_exception_when_creating_temporary_credentials_and_oauth_token_parameter_is_missing()
    {
        $this->setupResponseStub('oauth_token_secret=token_secret&oauth_callback_confirmed=true');

        $this->expectException(CredentialsException::class);

        $this->credentialsFactory->createTemporaryCredentialsFromResponse($this->responseStub);
    }

    /** @test */
    function it_throws_credentials_exception_when_creating_temporary_credentials_and_oauth_token_secret_parameter_is_missing()
    {
        $this->setupResponseStub('oauth_token=token_id&oauth_callback_confirmed=true');

        $this->expectException(CredentialsException::class);

        $this->credentialsFactory->createTemporaryCredentialsFromResponse($this->responseStub);
    }

    /** @test */
    function it_throws_credentials_exception_when_creating_temporary_credentials_and_oauth_callback_confirmed_parameter_is_missing()
    {
        $this->setupResponseStub('oauth_token=token_id&oauth_token_secret=token_secret');

        $this->expectException(CredentialsException::class);

        $this->credentialsFactory->createTemporaryCredentialsFromResponse($this->responseStub);
    }

    /** @test */
    function it_throws_credentials_exception_when_creating_temporary_credentials_and_oauth_callback_confirmed_is_not_true()
    {
        $this->setupResponseStub('oauth_token=token_id&oauth_token_secret=token_secret&oauth_callback_confirmed=false');

        $this->expectException(CredentialsException::class);

        $this->credentialsFactory->createTemporaryCredentialsFromResponse($this->responseStub);
    }

    /** @test */
    function it_can_create_temporary_credentials_from_response()
    {
        $this->setupResponseStub('oauth_token=token_id&oauth_token_secret=token_secret&oauth_callback_confirmed=true');

        $temporaryCredentials = $this->credentialsFactory->createTemporaryCredentialsFromResponse($this->responseStub);

        $this->assertInstanceOf(TemporaryCredentials::class, $temporaryCredentials);
        $this->assertEquals('token_id', $temporaryCredentials->getIdentifier());
        $this->assertEquals('token_secret', $temporaryCredentials->getSecret());
    }

    function setupResponseStub($body)
    {
        $this->responseStub
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->streamStub);

        $this->streamStub
            ->expects($this->once())
            ->method('getContents')
            ->willReturn($body);
    }
}
