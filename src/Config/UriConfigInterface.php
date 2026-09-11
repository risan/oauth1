<?php

declare(strict_types=1);

namespace Risan\OAuth1\Config;

use Psr\Http\Message\UriInterface;
use Risan\OAuth1\Request\UriParserInterface;

interface UriConfigInterface
{
    /**
     * Get the UriParserInterface implementation.
     */
    public function getParser(): UriParserInterface;

    /**
     * Get the base URI.
     */
    public function base(): ?UriInterface;

    /**
     * Check if base URI is set.
     */
    public function hasBase(): bool;

    /**
     * Get the URI for obtaining temporary credentials. Also known as request
     * token URI.
     */
    public function forTemporaryCredentials(): UriInterface;

    /**
     * Get the URI for asking user to authorize the request.
     */
    public function forAuthorization(): UriInterface;

    /**
     * Get the URI for obtaining token credentials. Also known as access token
     * URI.
     */
    public function forTokenCredentials(): UriInterface;

    /**
     * Get the callback URI.
     */
    public function callback(): UriInterface;

    /**
     * Check if callback URI is set.
     */
    public function hasCallback(): bool;

    /**
     * Parse and build the given URI.
     */
    public function build(UriInterface|string $uri): UriInterface;
}
