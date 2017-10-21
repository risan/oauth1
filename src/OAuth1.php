<?php

namespace Risan\OAuth1;

use InvalidArgumentException;
use Risan\OAuth1\Request\RequestFactoryInterface;
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
     * Create a new OAuth1 instance.
     *
     * @param \Risan\OAuth1\HttpClientInterface $httpClient
     * @param \Risan\OAuth1\Request\RequestFactoryInterface $request
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
    public function getTemporaryCredentials()
    {
        $response = $this->httpClient->send($this->requestFactory->createForTemporaryCredentials());

        return $this->credentialsFactory->createTemporaryCredentialsFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function buildAuthorizationUrl(TemporaryCredentials $temporaryCredentials)
    {
        return;
    }

    /**
     * {@inheritDoc}
     */
    public function getTokenCredentials(TemporaryCredentials $temporaryCredentials, $temporaryIdentifier, $verificationCode)
    {
        if ($temporaryCredentials->getIdentifier() !== $temporaryIdentifier) {
            throw new InvalidArgumentException('The given temporary identifier does not match the temporary credentials.');
        }

        $response = $this->httpClient->send(
            $this->requestFactory->createForTemporaryCredentials($temporaryCredentials, $verificationCode)
        );

        return $response;
    }
}
