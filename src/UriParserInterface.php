<?php

namespace Risan\OAuth1;

use Psr\Http\Message\UriInterface;

interface UriParserInterface
{
    /**
     * Check whether the given URI is absolute.
     *
     * @param  \Psr\Http\Message\UriInterface $uri
     * @return boolean
     */
    public function isAbsolute(UriInterface $uri);

    /**
     * Check if the given URI missing the scheme path.
     *
     * @param  \Psr\Http\Message\UriInterface $uri
     * @return boolean
     */
    public function isMissingScheme(UriInterface $uri);

    /**
     * Resolve the URI against the base URI.
     *
     * @param  \Psr\Http\Message\UriInterface $baseUri
     * @param  \Psr\Http\Message\UriInterface $relativeUri
     * @return \Psr\Http\Message\UriInterface
     */
    public function resolve(UriInterface $baseUri, UriInterface $uri);

    /**
     * Parse the given uri to the PSR URIInterface instance.
     *
     * @param  \Psr\Http\Message\UriInterface|string $uri
     * @return \Psr\Http\Message\UriInterface
     * @throws \InvalidArgumentException
     */
    public function toPsrUri($uri);
}
