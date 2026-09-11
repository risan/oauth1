<?php

declare(strict_types=1);

namespace Risan\OAuth1;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Risan\OAuth1\Config\ConfigInterface;
use Risan\OAuth1\Credentials\CredentialsException;
use Risan\OAuth1\Credentials\CredentialsFactoryInterface;
use Risan\OAuth1\Credentials\TemporaryCredentials;
use Risan\OAuth1\Credentials\TokenCredentials;
use Risan\OAuth1\Request\RequestFactoryInterface;

interface OAuth1Interface
{
    /**
     * Get the HttpClientInterface instance.
     */
    public function getHttpClient(): HttpClientInterface;

    /**
     * Get the RequestFactoryInterface instance.
     */
    public function getRequestFactory(): RequestFactoryInterface;

    /**
     * Get the CredentialsFactoryInterface instance.
     */
    public function getCredentialsFactory(): CredentialsFactoryInterface;

    /**
     * Get the ConfigInterface instance.
     */
    public function getConfig(): ConfigInterface;

    /**
     * Get TokenCredentials instance.
     */
    public function getTokenCredentials(): ?TokenCredentials;

    /**
     * Set the granted token credentials.
     *
     *
     * @return $this
     */
    public function setTokenCredentials(TokenCredentials $tokenCredentials): static;

    /**
     * Send request for obtaining temporary credentials.
     */
    public function requestTemporaryCredentials(): TemporaryCredentials;

    /**
     * Build the authorization URI.
     */
    public function buildAuthorizationUri(TemporaryCredentials $temporaryCredentials): string;

    /**
     * Send request for obtaining token credentials.
     *
     *
     * @throws \InvalidArgumentException
     */
    public function requestTokenCredentials(TemporaryCredentials $temporaryCredentials, string $temporaryIdentifier, string $verificationCode): TokenCredentials;

    /**
     * Send HTTP GET request for accessing protected resource.
     *
     * @param  string  $uri
     */
    public function get(UriInterface|string $uri, array $options = []): ResponseInterface;

    /**
     * Send HTTP POST request for accessing protected resource.
     *
     * @param  string  $uri
     */
    public function post(UriInterface|string $uri, array $options = []): ResponseInterface;

    /**
     * Send HTTP PUT request for accessing protected resource.
     *
     * @param  string  $uri
     */
    public function put(UriInterface|string $uri, array $options = []): ResponseInterface;

    /**
     * Send HTTP PATCH request for accessing protected resource.
     *
     * @param  string  $uri
     */
    public function patch(UriInterface|string $uri, array $options = []): ResponseInterface;

    /**
     * Send HTTP DELETE request for accessing protected resource.
     *
     * @param  string  $uri
     */
    public function delete(UriInterface|string $uri, array $options = []): ResponseInterface;

    /**
     * Send request for accessing protected resource.
     *
     * @param  string  $uri
     *
     * @throws CredentialsException
     */
    public function request(string $method, UriInterface|string $uri, array $options = []): ResponseInterface;
}
