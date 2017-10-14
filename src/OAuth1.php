<?php

namespace Risan\OAuth1;

use InvalidArgumentException;
use Risan\OAuth1\Credentials\ClientCredentials;
use Risan\OAuth1\Request\RequestConfigInterface;
use Risan\OAuth1\Credentials\TemporaryCredentials;
use Risan\OAuth1\Credentials\CredentialsFactoryInterface;

class OAuth1 implements OAuth1Interface
{
    /**
     * The RequestConfigInterface instance.
     *
     * @var \Risan\OAuth1\Request\RequestConfigInterface
     */
    protected $requestConfig;

    /**
     * The HttpClientInterface instance.
     *
     * @var \Risan\OAuth1\HttpClientInterface
     */
    protected $httpClient;

    /**
     * The CredentialsFactoryInterface instance.
     *
     * @var \Risan\OAuth1\Credentials\CredentialsFactoryInterface
     */
    protected $credentialsFactory;

    /**
     * Create a new OAuth1 instance.
     *
     * @param \Risan\OAuth1\Request\RequestConfigInterface $requestConfig
     * @param \Risan\OAuth1\HttpClientInterface $httpClient
     * @param \Risan\OAuth1\Credentials\CredentialsFactoryInterface $credentialsFactory
     */
    public function __construct(RequestConfigInterface $requestConfig, HttpClientInterface $httpClient, CredentialsFactoryInterface $credentialsFactory)
    {
        $this->requestConfig = $requestConfig;
        $this->httpClient = $httpClient;
        $this->credentialsFactory = $credentialsFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getRequestConfig()
    {
        return $this->requestConfig;
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
    public function getCredentialsFactory()
    {
        return $this->credentialsFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getTemporaryCredentials()
    {
        $response = $this->httpClient->post($this->requestConfig->getTemporaryCredentialsUrl(), [
            'headers' => [
                'Authorization' => $this->requestConfig->getTemporaryCredentialsAuthorizationHeader(),
            ],
        ]);

        return $this->credentialsFactory->createTemporaryCredentialsFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function buildAuthorizationUrl(TemporaryCredentials $temporaryCredentials)
    {
        return $this->requestConfig->buildAuthorizationUrl($temporaryCredentials);
    }

    /**
     * {@inheritDoc}
     */
    public function getTokenCredentials(TemporaryCredentials $temporaryCredentials, $temporaryIdentifier, $verificationCode)
    {
        if ($temporaryCredentials->getIdentifier() !== $temporaryIdentifier) {
            throw new InvalidArgumentException('The given temporary identifier does not match the temporary credentials.');
        }

        $response = $this->httpClient->post($this->requestConfig->getTokenCredentialsUrl(), [
            'headers' => [
                'Authorization' => $this->requestConfig->getTokenCredentialsAuthorizationHeader($temporaryCredentials, $verificationCode),
            ],
            'form_params' => [
                'oauth_verifier' => $verificationCode,
            ],
        ]);

        return $response;
    }
}
