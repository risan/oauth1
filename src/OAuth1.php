<?php

namespace Risan\OAuth1;

use Risan\OAuth1\Credentials\ClientCredentials;
use Risan\OAuth1\Request\RequestConfigInterface;

class OAuth1 implements OAuth1Interface
{
    /**
     * The RequestConfigInterface instance.
     *
     * @var \Risan\OAuth1\Request\RequestConfig
     */
    protected $requestConfig;

    /**
     * The HttpClientInterface instance.
     *
     * @var \Risan\OAuth1\HttpClientInterface
     */
    protected $httpClient;

    /**
     * Create a new OAuth1 instance.
     *
     * @param \Risan\OAuth1\Request\RequestConfigInterface $requestConfig
     * @param \Risan\OAuth1\HttpClientInterface $httpClient
     */
    public function __construct(RequestConfigInterface $requestConfig, HttpClientInterface $httpClient)
    {
        $this->requestConfig = $requestConfig;
        $this->httpClient = $httpClient;
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
    public function getTemporaryCredentials()
    {
        $response = $this->httpClient->post($this->requestConfig->getTemporaryCredentialsUrl(), [
            'headers' => [
                'Authorization' => $this->requestConfig->getTemporaryCredentialsAuthorizationHeader(),
            ],
        ]);

        return ClientCredentials::createFromResponse($response);
    }
}
