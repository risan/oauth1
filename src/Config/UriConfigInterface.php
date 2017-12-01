<?php

namespace Risan\OAuth1\Config;

interface UriConfigInterface
{
    /**
     * Get the UriParserInterface implementation.
     *
     * @return \Risan\OAuth1\Request\UriParserInterface
     */
    public function getParser();

    /**
     * Get the base URI.
     *
     * @return \Psr\Http\Message\UriInterface|null
     */
    public function base();

    /**
     * Check if base URI is set.
     *
     * @return boolean
     */
    public function hasBase();

    /**
     * Get the URI for obtaining temporary credentials. Also known as request
     * token URI.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function forTemporaryCredentials();

    /**
     * Get the URI for asking user to authorize the request.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function forAuthorization();

    /**
     * Get the URI for obtaining token credentials. Also known as access token
     * URI.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function forTokenCredentials();

    /**
     * Get the callback URI.
     *
     * @return \Psr\Http\Message\UriInterface|null
     */
    public function callback();

    /**
     * Check if callback URI is set.
     *
     * @return boolean
     */
    public function hasCallback();

    /**
     * Parse and build the given URI.
     *
     * @param  \Psr\Http\Message\UriInterface|string $uri
     * @return \Psr\Http\Message\UriInterface
     */
    public function build($uri);
}
