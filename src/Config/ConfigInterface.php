<?php

declare(strict_types=1);

namespace Risan\OAuth1\Config;

use Psr\Http\Message\UriInterface;
use Risan\OAuth1\Credentials\ClientCredentials;

interface ConfigInterface
{
    /**
     * Get the ClientCredentials instance.
     */
    public function getClientCredentials(): ClientCredentials;

    /**
     * Get the client credentials identifer.
     */
    public function getClientCredentialsIdentifier(): string;

    /**
     * Get the client credentials secret.
     */
    public function getClientCredentialsSecret(): string;

    /**
     * Get the UriConfigInterface instance.
     */
    public function getUri(): UriConfigInterface;

    /**
     * Get the URI for obtaining temporary credentials. Also known as request
     * token URI.
     */
    public function getTemporaryCredentialsUri(): UriInterface;

    /**
     * Get the URI for asking user to authorize the request.
     */
    public function getAuthorizationUri(): UriInterface;

    /**
     * Get the URI for obtaining token credentials. Also known as access token
     * URI.
     */
    public function getTokenCredentialsUri(): UriInterface;

    /** Get the HTTP method used to obtain temporary credentials. */
    public function getTemporaryCredentialsMethod(): string;

    /** Get the HTTP method used to obtain token credentials. */
    public function getTokenCredentialsMethod(): string;

    /**
     * Get the callback URI.
     */
    public function getCallbackUri(): UriInterface;

    /**
     * Check if callback URI is set.
     */
    public function hasCallbackUri(): bool;

    /**
     * Parse and build the given URI.
     */
    public function buildUri(UriInterface|string $uri): UriInterface;
}
