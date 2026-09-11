<?php

declare(strict_types=1);

namespace Risan\OAuth1\Test\Unit\Request;

use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;
use Risan\OAuth1\Config\ConfigInterface;
use Risan\OAuth1\Credentials\TemporaryCredentials;
use Risan\OAuth1\Credentials\TokenCredentials;
use Risan\OAuth1\Request\AuthorizationHeaderInterface;
use Risan\OAuth1\Request\RequestFactory;
use Risan\OAuth1\Request\RequestFactoryInterface;
use Risan\OAuth1\Request\RequestInterface;
use Risan\OAuth1\Request\UriParserInterface;

class RequestFactoryTest extends TestCase
{
    private $authorizationHeaderStub;

    private $uriParserStub;

    private $configStub;

    private $requestFactory;

    private $requestFactoryStub;

    private $requestStub;

    private $temporaryCredentialsStub;

    private $tokenCredentialsStub;

    private $psrUriStub;

    protected function setUp(): void
    {
        $this->authorizationHeaderStub = $this->createMock(AuthorizationHeaderInterface::class);
        $this->uriParserStub = $this->createMock(UriParserInterface::class);
        $this->configStub = $this->createMock(ConfigInterface::class);
        $this->requestFactory = new RequestFactory($this->authorizationHeaderStub, $this->uriParserStub);
        $this->requestStub = $this->createMock(RequestInterface::class);
        $this->temporaryCredentialsStub = $this->createMock(TemporaryCredentials::class);
        $this->tokenCredentialsStub = $this->createMock(TokenCredentials::class);
        $this->psrUriStub = $this->createMock(UriInterface::class);

        $this->requestFactoryStub = $this->getMockBuilder(RequestFactory::class)
            ->setConstructorArgs([$this->authorizationHeaderStub, $this->uriParserStub])
            ->onlyMethods(['getConfig', 'create'])
            ->getMock();
    }

    #[Test]
    public function it_implements_request_factory_interface()
    {
        $this->assertInstanceOf(RequestFactoryInterface::class, $this->requestFactory);
    }

    #[Test]
    public function it_can_get_authorization_header()
    {
        $this->assertSame($this->authorizationHeaderStub, $this->requestFactory->getAuthorizationHeader());
    }

    #[Test]
    public function it_can_get_uri_parser()
    {
        $this->assertSame($this->uriParserStub, $this->requestFactory->getUriParser());
    }

    #[Test]
    public function it_can_get_config()
    {
        $this->authorizationHeaderStub
            ->expects($this->once())
            ->method('getConfig')
            ->willReturn($this->configStub);

        $this->assertSame($this->configStub, $this->requestFactory->getConfig());
    }

    #[Test]
    public function it_can_create_for_temporary_credentials()
    {
        $this->requestFactoryStub
            ->expects($this->once())
            ->method('getConfig')
            ->willReturn($this->configStub);

        $this->configStub
            ->expects($this->once())
            ->method('getTemporaryCredentialsMethod')
            ->willReturn('POST');

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

    #[Test]
    public function it_can_build_authorization_uri()
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

    #[Test]
    public function it_can_create_for_token_credentials()
    {
        $this->requestFactoryStub
            ->expects($this->once())
            ->method('getConfig')
            ->willReturn($this->configStub);

        $this->configStub
            ->expects($this->once())
            ->method('getTokenCredentialsMethod')
            ->willReturn('POST');

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

    #[Test]
    public function it_puts_the_verifier_in_the_query_for_a_get_token_endpoint()
    {
        $this->requestFactoryStub
            ->expects($this->once())
            ->method('getConfig')
            ->willReturn($this->configStub);
        $this->configStub->method('getTokenCredentialsMethod')->willReturn('GET');
        $this->configStub->method('getTokenCredentialsUri')->willReturn($this->psrUriStub);
        $this->psrUriStub->method('__toString')->willReturn('https://example.com/access_token');
        $this->authorizationHeaderStub
            ->method('forTokenCredentials')
            ->willReturn('OAuth signed');
        $this->requestFactoryStub
            ->expects($this->once())
            ->method('create')
            ->with('GET', 'https://example.com/access_token', [
                'headers' => ['Authorization' => 'OAuth signed'],
                'query' => ['oauth_verifier' => 'verification_code'],
            ])
            ->willReturn($this->requestStub);

        $this->assertSame(
            $this->requestStub,
            $this->requestFactoryStub->createForTokenCredentials($this->temporaryCredentialsStub, 'verification_code'),
        );
    }

    #[Test]
    public function it_can_create_for_protected_resource()
    {
        $this->requestFactoryStub
            ->expects($this->once())
            ->method('getConfig')
            ->willReturn($this->configStub);

        $this->configStub
            ->expects($this->once())
            ->method('buildUri')
            ->with('http://example.com')
            ->willReturn($this->psrUriStub);

        $this->psrUriStub
            ->expects($this->once())
            ->method('__toString')
            ->willReturn('http://example.com');

        $this->authorizationHeaderStub
            ->expects($this->once())
            ->method('forProtectedResource')
            ->with($this->tokenCredentialsStub, 'GET', $this->psrUriStub, ['foo' => 'bar'])
            ->willReturn('OAuth1');

        $this->requestFactoryStub
            ->expects($this->once())
            ->method('create')
            ->with('GET', 'http://example.com', [
                'headers' => ['Authorization' => 'OAuth1'],
                'foo' => 'bar',
            ])
            ->willReturn($this->requestStub);

        $this->assertSame(
            $this->requestStub,
            $this->requestFactoryStub->createForProtectedResource($this->tokenCredentialsStub, 'GET', 'http://example.com', ['foo' => 'bar'])
        );
    }

    #[Test]
    public function it_preserves_repeated_query_and_form_parameters_on_the_wire()
    {
        $uri = new Uri('https://example.com/resource');
        $signedUri = new Uri('https://example.com/resource?tag=one&tag=two');
        $options = [
            'query' => [['tag', 'one'], ['tag', 'two']],
            'form_params' => [['item', 'first'], ['item', 'second']],
        ];

        $this->authorizationHeaderStub
            ->expects($this->once())
            ->method('forProtectedResource')
            ->with($this->tokenCredentialsStub, 'POST', $signedUri, [
                'body' => 'item=first&item=second',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
            ])
            ->willReturn('OAuth signed');
        $this->authorizationHeaderStub
            ->expects($this->once())
            ->method('getConfig')
            ->willReturn($this->configStub);
        $this->configStub
            ->expects($this->once())
            ->method('buildUri')
            ->with($uri)
            ->willReturn($uri);

        $request = $this->requestFactory->createForProtectedResource($this->tokenCredentialsStub, 'POST', $uri, $options);

        $this->assertSame('https://example.com/resource?tag=one&tag=two', $request->getUri());
        $this->assertArrayNotHasKey('query', $request->getOptions());
        $this->assertSame('item=first&item=second', $request->getOptions()['body']);
        $this->assertSame('application/x-www-form-urlencoded', $request->getOptions()['headers']['Content-Type']);
        $this->assertSame('OAuth signed', $request->getOptions()['headers']['Authorization']);
        $this->assertArrayNotHasKey('form_params', $request->getOptions());
    }
}
