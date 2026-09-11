<?php

declare(strict_types=1);

namespace Risan\OAuth1\Request;

use Psr\Http\Message\UriInterface;
use Risan\OAuth1\Config\ConfigInterface;
use Risan\OAuth1\Credentials\TemporaryCredentials;
use Risan\OAuth1\Credentials\TokenCredentials;

interface RequestFactoryInterface
{
    /**
     * Get the AuthorizationHeaderInterface instance.
     */
    public function getAuthorizationHeader(): AuthorizationHeaderInterface;

    /**
     * Get the ConfigInterface instance.
     */
    public function getConfig(): ConfigInterface;

    /**
     * Get the UriParserInterface instance.
     */
    public function getUriParser(): UriParserInterface;

    /**
     * Create request for obtaining temporary credentials.
     */
    public function createForTemporaryCredentials(): RequestInterface;

    /**
     * Build the authorization URI.
     */
    public function buildAuthorizationUri(TemporaryCredentials $temporaryCredentials): UriInterface;

    /**
     * Create request for obtaining token credentials.
     */
    public function createForTokenCredentials(TemporaryCredentials $temporaryCredentials, string $verificationCode): RequestInterface;

    /**
     * Create an authenticated request for obtaining protected resource.
     *
     * @param  string  $uri
     */
    public function createForProtectedResource(TokenCredentials $tokenCredentials, string $method, UriInterface|string $uri, array $options = []): RequestInterface;
}
