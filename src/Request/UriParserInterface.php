<?php

declare(strict_types=1);

namespace Risan\OAuth1\Request;

use Psr\Http\Message\UriInterface;

interface UriParserInterface
{
    /**
     * Check whether the given URI is absolute.
     */
    public function isAbsolute(UriInterface $uri): bool;

    /**
     * Check if the given URI missing the scheme path.
     */
    public function isMissingScheme(UriInterface $uri): bool;

    /**
     * Build URI from parts.
     */
    public function buildFromParts(array $parts): UriInterface;

    /**
     * Resolve the URI against the base URI.
     */
    public function resolve(UriInterface $baseUri, UriInterface $uri): UriInterface;

    /**
     * Append query parameters to the URI.
     */
    public function appendQueryParameters(UriInterface $uri, array $parameters = []): UriInterface;

    /**
     * Parse the given uri to the PSR URIInterface instance.
     *
     *
     * @throws \InvalidArgumentException
     */
    public function toPsrUri(UriInterface|string $uri): UriInterface;
}
