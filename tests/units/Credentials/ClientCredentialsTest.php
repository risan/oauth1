<?php

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;
use Risan\OAuth1\Credentials\ClientCredentials;
use Risan\OAuth1\Credentials\CredentialsException;
use Risan\OAuth1\Credentials\CredentialsInterface;

class ClientCredentialsTest extends TestCase
{
    private $clientCredentials;
    private $responseStub;
    private $streamStub;

    function setUp()
    {
        $this->clientCredentials = new ClientCredentials('foo', 'bar');
        $this->responseStub = $this->createMock(ResponseInterface::class);
        $this->streamStub = $this->createMock(StreamInterface::class);
    }

    /** @test */
    function client_credentials_must_be_an_instance_of_credentials_interface()
    {
        $this->assertInstanceOf(CredentialsInterface::class, $this->clientCredentials);
    }

    /** @test */
    function client_credentials_can_get_identifier()
    {
        $this->assertEquals('foo', $this->clientCredentials->getIdentifier());
    }

    /** @test */
    function client_credentials_can_get_secret()
    {
        $this->assertEquals('bar', $this->clientCredentials->getSecret());
    }

    /** @test */
    function client_credentials_will_throw_credentials_exception_if_oauth_token_parameter_is_missing()
    {
        $this->responseStub
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->streamStub);

        $this->streamStub
            ->expects($this->once())
            ->method('getContents')
            ->willReturn('oauth_secret=token_secret&oauth_callback_confirmed=true');

        $this->expectException(CredentialsException::class);

        ClientCredentials::createFromResponse($this->responseStub);
    }

    /** @test */
    function client_credentials_will_throw_credentials_exception_if_oauth_secret_parameter_is_missing()
    {
        $this->responseStub
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->streamStub);

        $this->streamStub
            ->expects($this->once())
            ->method('getContents')
            ->willReturn('oauth_token=token_id&oauth_callback_confirmed=true');

        $this->expectException(CredentialsException::class);

        ClientCredentials::createFromResponse($this->responseStub);
    }

    /** @test */
    function client_credentials_will_throw_credentials_exception_if_oauth_callback_confirmed_parameter_is_missing()
    {
        $this->responseStub
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->streamStub);

        $this->streamStub
            ->expects($this->once())
            ->method('getContents')
            ->willReturn('oauth_token=token_id&oauth_secret=token_secret');

        $this->expectException(CredentialsException::class);

        ClientCredentials::createFromResponse($this->responseStub);
    }

    /** @test */
    function client_credentials_will_throw_credentials_exception_if_oauth_callback_confirmed_is_not_true()
    {
        $this->responseStub
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->streamStub);

        $this->streamStub
            ->expects($this->once())
            ->method('getContents')
            ->willReturn('oauth_token=token_id&oauth_secret=token_secret&oauth_callback_confirmed=false');

        $this->expectException(CredentialsException::class);

        ClientCredentials::createFromResponse($this->responseStub);
    }

    /** @test */
    function client_credentials_can_be_created_from_a_valid_response()
    {
        $this->responseStub
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->streamStub);

        $this->streamStub
            ->expects($this->once())
            ->method('getContents')
            ->willReturn('oauth_token=token_id&oauth_secret=token_secret&oauth_callback_confirmed=true');

        $clientCredentials = ClientCredentials::createFromResponse($this->responseStub);

        $this->assertInstanceOf(ClientCredentials::class, $clientCredentials);
        $this->assertEquals('token_id', $clientCredentials->getIdentifier());
        $this->assertEquals('token_secret', $clientCredentials->getSecret());
    }
}
