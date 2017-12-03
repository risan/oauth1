<?php

namespace Risan\OAuth1;

use Risan\OAuth1\Credentials\TokenCredentials;
use Risan\OAuth1\Credentials\TemporaryCredentials;

interface OAuth1Interface
{
    /**
     * Get the HttpClientInterface instance.
     *
     * @return \Risan\OAuth1\HttpClientInterface
     */
    public function getHttpClient();

    /**
     * Get the RequestFactoryInterface instance.
     *
     * @return \Risan\OAuth1\Request\RequestFactoryInterface
     */
    public function getRequestFactory();

    /**
     * Get the CredentialsFactoryInterface instance.
     *
     * @return \Risan\OAuth1\Credentials\CredentialsFactoryInterface
     */
    public function getCredentialsFactory();

    /**
     * Get the ConfigInterface instance.
     *
     * @return \Risan\OAuth1\Config\ConfigInterface
     */
    public function getConfig();

    /**
     * Get TokenCredentials instance.
     *
     * @return \Risan\OAuth1\Credentials\TokenCredentials|null
     */
    public function getTokenCredentials();

    /**
     * Set the granted token credentials.
     *
     * @param \Risan\OAuth1\Credentials\TokenCredentials $tokenCredentials
     *
     * @return $this
     */
    public function setTokenCredentials(TokenCredentials $tokenCredentials);

    /**
     * Send request for obtaining temporary credentials.
     *
     * @return \Risan\OAuth1\Credentials\TemporaryCredentials
     */
    public function requestTemporaryCredentials();

    /**
     * Build the authorization URI.
     *
     * @param \Risan\OAuth1\Credentials\TemporaryCredentials $temporaryCredentials
     *
     * @return string
     */
    public function buildAuthorizationUri(TemporaryCredentials $temporaryCredentials);

    /**
     * Send request for obtaining token credentials.
     *
     * @param \Risan\OAuth1\Credentials\TemporaryCredentials $temporaryCredentials
     * @param string                                         $temporaryIdentifier
     * @param string                                         $verificationCode
     *
     * @return \Risan\OAuth1\Credentials\TokenCredentials
     *
     * @throws \InvalidArgumentException
     */
    public function requestTokenCredentials(TemporaryCredentials $temporaryCredentials, $temporaryIdentifier, $verificationCode);

    /**
     * Send HTTP GET request for accessing protected resource.
     *
     * @param string $uri
     * @param array  $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function get($uri, array $options = []);

    /**
     * Send HTTP POST request for accessing protected resource.
     *
     * @param string $uri
     * @param array  $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function post($uri, array $options = []);

    /**
     * Send HTTP PUT request for accessing protected resource.
     *
     * @param string $uri
     * @param array  $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function put($uri, array $options = []);

    /**
     * Send HTTP PATCH request for accessing protected resource.
     *
     * @param string $uri
     * @param array  $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function patch($uri, array $options = []);

    /**
     * Send HTTP DELETE request for accessing protected resource.
     *
     * @param string $uri
     * @param array  $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function delete($uri, array $options = []);

    /**
     * Send request for accessing protected resource.
     *
     * @param string $method
     * @param string $uri
     * @param array  $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @throws \Risan\OAuth1\Credentials\CredentialsException
     */
    public function request($method, $uri, array $options = []);
}
