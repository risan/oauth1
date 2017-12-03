<?php

namespace Risan\OAuth1\Request;

use Psr\Http\Message\UriInterface;

interface UriParserInterface
{
    /**
     * Check whether the given URI is absolute.
     *
     * @param \Psr\Http\Message\UriInterface $uri
     *
     * @return bool
     */
    public function isAbsolute(UriInterface $uri);

    /**
     * Check if the given URI missing the scheme path.
     *
     * @param \Psr\Http\Message\UriInterface $uri
     *
     * @return bool
     */
    public function isMissingScheme(UriInterface $uri);

    /**
     * Build URI from parts.
     *
     * @param array $parts
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function buildFromParts(array $parts);

    /**
     * Resolve the URI against the base URI.
     *
     * @param \Psr\Http\Message\UriInterface $baseUri
     * @param \Psr\Http\Message\UriInterface $relativeUri
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function resolve(UriInterface $baseUri, UriInterface $uri);

    /**
     * Append query parameters to the URI.
     *
     * @param \Psr\Http\Message\UriInterface $uri
     * @param array                          $parameters
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function appendQueryParameters(UriInterface $uri, array $parameters = []);

    /**
     * Parse the given uri to the PSR URIInterface instance.
     *
     * @param \Psr\Http\Message\UriInterface|string $uri
     *
     * @return \Psr\Http\Message\UriInterface
     *
     * @throws \InvalidArgumentException
     */
    public function toPsrUri($uri);
}
