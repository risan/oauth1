<?php

declare(strict_types=1);

namespace Risan\OAuth1\Test\Unit\Credentials;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Risan\OAuth1\Credentials\CredentialsException;
use Risan\OAuth1\Credentials\CredentialsFactory;
use Risan\OAuth1\Credentials\CredentialsFactoryInterface;
use Risan\OAuth1\Credentials\TemporaryCredentials;
use Risan\OAuth1\Credentials\TokenCredentials;

class CredentialsFactoryTest extends TestCase
{
    private $credentialsFactory;

    private $responseStub;

    private $streamStub;

    protected function setUp(): void
    {
        $this->credentialsFactory = new CredentialsFactory;
        $this->responseStub = $this->createMock(ResponseInterface::class);
        $this->streamStub = $this->createMock(StreamInterface::class);
    }

    #[Test]
    public function it_implements_credentials_factory_interface()
    {
        $this->assertInstanceOf(CredentialsFactoryInterface::class, $this->credentialsFactory);
    }

    #[Test]
    public function it_can_get_parameters_from_response()
    {
        $this->setupResponseStub('foo=bar');

        $this->assertSame(
            ['foo' => 'bar'],
            $this->credentialsFactory->getParametersFromResponse($this->responseStub)
        );
    }

    #[Test]
    public function it_can_get_missing_parameter_key()
    {
        $this->assertEquals(
            'baz',
            $this->credentialsFactory->getMissingParameterKey(['foo' => 'bar'], ['foo', 'baz'])
        );

        $this->assertNull(
            $this->credentialsFactory->getMissingParameterKey(['foo' => 'bar'], ['foo'])
        );
    }

    #[Test]
    public function it_throws_exception_when_creating_temporary_credentials_and_oauth_token_is_missing()
    {
        $this->setupResponseStub('oauth_token_secret=token_secret&oauth_callback_confirmed=true');
        $this->expectException(CredentialsException::class);
        $this->credentialsFactory->createTemporaryCredentialsFromResponse($this->responseStub);
    }

    #[Test]
    public function it_throws_exception_when_creating_temporary_credentials_and_oauth_token_secret_is_missing()
    {
        $this->setupResponseStub('oauth_token=token_id&oauth_callback_confirmed=true');
        $this->expectException(CredentialsException::class);
        $this->credentialsFactory->createTemporaryCredentialsFromResponse($this->responseStub);
    }

    #[Test]
    public function it_throws_exception_when_creating_temporary_credentials_and_oauth_callback_confirmed_is_missing()
    {
        $this->setupResponseStub('oauth_token=token_id&oauth_token_secret=token_secret');
        $this->expectException(CredentialsException::class);
        $this->credentialsFactory->createTemporaryCredentialsFromResponse($this->responseStub);
    }

    #[Test]
    public function it_throws_exception_when_creating_temporary_credentials_and_oauth_callback_confirmed_is_not_true()
    {
        $this->setupResponseStub('oauth_token=token_id&oauth_token_secret=token_secret&oauth_callback_confirmed=false');
        $this->expectException(CredentialsException::class);
        $this->credentialsFactory->createTemporaryCredentialsFromResponse($this->responseStub);
    }

    #[Test]
    public function it_can_create_temporary_credentials_from_response()
    {
        $this->setupResponseStub('oauth_token=token_id&oauth_token_secret=token_secret&oauth_callback_confirmed=true');
        $temporaryCredentials = $this->credentialsFactory->createTemporaryCredentialsFromResponse($this->responseStub);
        $this->assertInstanceOf(TemporaryCredentials::class, $temporaryCredentials);
        $this->assertEquals('token_id', $temporaryCredentials->getIdentifier());
        $this->assertEquals('token_secret', $temporaryCredentials->getSecret());
    }

    #[Test]
    public function it_throws_exception_when_creating_token_credentials_and_oauth_token_is_missing()
    {
        $this->setupResponseStub('oauth_token_secret=token_secret');
        $this->expectException(CredentialsException::class);
        $this->credentialsFactory->createTokenCredentialsFromResponse($this->responseStub);
    }

    #[Test]
    public function it_throws_exception_when_creating_token_credentials_and_oauth_token_secret_is_missing()
    {
        $this->setupResponseStub('oauth_token=token_id');
        $this->expectException(CredentialsException::class);
        $this->credentialsFactory->createTokenCredentialsFromResponse($this->responseStub);
    }

    #[Test]
    public function it_rejects_empty_token_credentials()
    {
        $this->setupResponseStub('oauth_token=&oauth_token_secret=token_secret');
        $this->expectException(CredentialsException::class);

        $this->credentialsFactory->createTokenCredentialsFromResponse($this->responseStub);
    }

    #[Test]
    public function it_can_create_token_credentials_from_response()
    {
        $this->setupResponseStub('oauth_token=token_id&oauth_token_secret=token_secret');
        $tokenCredentials = $this->credentialsFactory->createTokenCredentialsFromResponse($this->responseStub);
        $this->assertInstanceOf(TokenCredentials::class, $tokenCredentials);
        $this->assertEquals('token_id', $tokenCredentials->getIdentifier());
        $this->assertEquals('token_secret', $tokenCredentials->getSecret());
    }

    public function setupResponseStub($body)
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
