<?php

namespace Risan\OAuth1;

use InvalidArgumentException;
use Risan\OAuth1\Credentials\TokenCredentials;
use Risan\OAuth1\Request\RequestFactoryInterface;
use Risan\OAuth1\Credentials\CredentialsException;
use Risan\OAuth1\Credentials\TemporaryCredentials;
use Risan\OAuth1\Credentials\CredentialsFactoryInterface;

class OAuth1 implements OAuth1Interface
{
    /**
     * The HttpClientInterface instance.
     *
     * @var \Risan\OAuth1\HttpClientInterface
     */
    protected $httpClient;

    /**
     * The RequestFactoryInterface instance.
     *
     * @var \Risan\OAuth1\Request\RequestFactoryInterface
     */
    protected $requestFactory;

    /**
     * The CredentialsFactoryInterface instance.
     *
     * @var \Risan\OAuth1\Credentials\CredentialsFactoryInterface
     */
    protected $credentialsFactory;

    /**
     * The TokenCredentials instance.
     *
     * @var \Risan\OAuth1\Credentials\TokenCredentials
     */
    protected $tokenCredentials;

    /**
     * Create a new OAuth1 instance.
     *
     * @param \Risan\OAuth1\HttpClientInterface $httpClient
     * @param \Risan\OAuth1\Request\RequestFactoryInterface $requestFactory
     * @param \Risan\OAuth1\Credentials\CredentialsFactoryInterface $credentialsFactory
     */
    public function __construct(HttpClientInterface $httpClient, RequestFactoryInterface $requestFactory, CredentialsFactoryInterface $credentialsFactory)
    {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->credentialsFactory = $credentialsFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * {@inheritDoc}
     */
    public function getRequestFactory()
    {
        return $this->requestFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getCredentialsFactory()
    {
        return $this->credentialsFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return $this->requestFactory->getConfig();
    }

    /**
     * {@inheritDoc}
     */
    public function getTokenCredentials()
    {
        return $this->tokenCredentials;
    }

    /**
     * {@inheritDoc}
     */
    public function setTokenCredentials(TokenCredentials $tokenCredentials)
    {
        $this->tokenCredentials = $tokenCredentials;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function requestTemporaryCredentials()
    {
        $response = $this->httpClient->send($this->requestFactory->createForTemporaryCredentials());

        return $this->credentialsFactory->createTemporaryCredentialsFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function buildAuthorizationUri(TemporaryCredentials $temporaryCredentials)
    {
        return (string) $this->requestFactory->buildAuthorizationUri($temporaryCredentials);
    }

    /**
     * {@inheritDoc}
     */
    public function requestTokenCredentials(TemporaryCredentials $temporaryCredentials, $temporaryIdentifier, $verificationCode)
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
     * {@inheritDoc}
     */
    public function request($method, $uri, array $options = [])
    {
        if (null === $this->getTokenCredentials()) {
            throw new CredentialsException('No token credential has been set.');
        }

        return $this->httpClient->send(
            $this->requestFactory->createForProtectedResource($this->getTokenCredentials(), $method, $uri, $options)
        );
    }
}
