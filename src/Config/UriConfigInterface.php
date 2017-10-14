<?php

namespace Risan\OAuth1\Config;

interface UriConfigInterface
{
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
    public function temporaryCredentials();

    /**
     * Get the URI for asking user to authorize the request.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function authorization();

    /**
     * Get the URI for obtaining token credentials. Also known as access token
     * URI.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function tokenCredentials();

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
}
