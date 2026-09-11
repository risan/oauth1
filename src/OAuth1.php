<?php

declare(strict_types=1);

namespace Risan\OAuth1;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Risan\OAuth1\Config\ConfigInterface;
use Risan\OAuth1\Credentials\CredentialsException;
use Risan\OAuth1\Credentials\CredentialsFactoryInterface;
use Risan\OAuth1\Credentials\TemporaryCredentials;
use Risan\OAuth1\Credentials\TokenCredentials;
use Risan\OAuth1\Request\RequestFactoryInterface;

class OAuth1 implements OAuth1Interface
{
    /**
     * The HttpClientInterface instance.
     */
    protected HttpClientInterface $httpClient;

    /**
     * The RequestFactoryInterface instance.
     */
    protected RequestFactoryInterface $requestFactory;

    /**
     * The CredentialsFactoryInterface instance.
     */
    protected CredentialsFactoryInterface $credentialsFactory;

    /**
     * The TokenCredentials instance.
     */
    protected ?TokenCredentials $tokenCredentials = null;

    /**
     * Create a new OAuth1 instance.
     */
    public function __construct(HttpClientInterface $httpClient, RequestFactoryInterface $requestFactory, CredentialsFactoryInterface $credentialsFactory)
    {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->credentialsFactory = $credentialsFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getHttpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestFactory(): RequestFactoryInterface
    {
        return $this->requestFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentialsFactory(): CredentialsFactoryInterface
    {
        return $this->credentialsFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig(): ConfigInterface
    {
        return $this->requestFactory->getConfig();
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenCredentials(): ?TokenCredentials
    {
        return $this->tokenCredentials;
    }

    /**
     * {@inheritdoc}
     */
    public function setTokenCredentials(TokenCredentials $tokenCredentials): static
    {
        $this->tokenCredentials = $tokenCredentials;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function requestTemporaryCredentials(): TemporaryCredentials
    {
        $response = $this->httpClient->send($this->requestFactory->createForTemporaryCredentials());

        return $this->credentialsFactory->createTemporaryCredentialsFromResponse($response);
    }

    /**
     * {@inheritdoc}
     */
    public function buildAuthorizationUri(TemporaryCredentials $temporaryCredentials): string
    {
        return (string) $this->requestFactory->buildAuthorizationUri($temporaryCredentials);
    }

    /**
     * {@inheritdoc}
     */
    public function requestTokenCredentials(TemporaryCredentials $temporaryCredentials, string $temporaryIdentifier, string $verificationCode): TokenCredentials
    {
        if ($temporaryCredentials->getIdentifier() !== $temporaryIdentifier) {
            throw new InvalidArgumentException('The given temporary credentials identifier does not match the temporary credentials.');
        }

        $response = $this->httpClient->send(
            $this->requestFactory->createForTokenCredentials($temporaryCredentials, $verificationCode)
        );

        return $this->credentialsFactory->createTokenCredentialsFromResponse($response);
    }

    /**
     * {@inheritdoc}
     */
    public function get(UriInterface|string $uri, array $options = []): ResponseInterface
    {
        return $this->request('GET', $uri, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function post(UriInterface|string $uri, array $options = []): ResponseInterface
    {
        return $this->request('POST', $uri, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function put(UriInterface|string $uri, array $options = []): ResponseInterface
    {
        return $this->request('PUT', $uri, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function patch(UriInterface|string $uri, array $options = []): ResponseInterface
    {
        return $this->request('PATCH', $uri, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(UriInterface|string $uri, array $options = []): ResponseInterface
    {
        return $this->request('DELETE', $uri, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function request(string $method, UriInterface|string $uri, array $options = []): ResponseInterface
    {
        if ($this->getTokenCredentials() === null) {
            throw new CredentialsException('No token credential has been set.');
        }

        return $this->httpClient->send(
            $this->requestFactory->createForProtectedResource($this->getTokenCredentials(), $method, $uri, $options)
        );
    }
}
