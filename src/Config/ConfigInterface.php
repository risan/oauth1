<?php

namespace Risan\OAuth1\Config;

interface ConfigInterface
{
    /**
     * Get the ClientCredentials instance.
     *
     * @return \Risan\OAuth1\Credentials\ClientCredentials
     */
    public function getClientCredentials();

    /**
     * Get the client credentials identifer.
     *
     * @return string
     */
    public function getClientCredentialsIdentifier();

    /**
     * Get the client credentials secret.
     *
     * @return string
     */
    public function getClientCredentialsSecret();

    /**
     * Get the UriConfigInterface instance.
     *
     * @return \Risan\OAuth1\Config\UriConfigInterface
     */
    public function getUri();

    /**
     * Get the URI for obtaining temporary credentials. Also known as request
     * token URI.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getTemporaryCredentialsUri();

    /**
     * Get the URI for asking user to authorize the request.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getAuthorizationUri();

    /**
     * Get the URI for obtaining token credentials. Also known as access token
     * URI.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getTokenCredentialsUri();

    /**
     * Check if callback URI is set.
     *
     * @return boolean
     */
    public function hasCallbackUri();

    /**
     * Get the callback URI.
     *
     * @return \Psr\Http\Message\UriInterface|null
     */
    public function getCallbackUri();

    /**
     * Create an instance of ConfigureInterface from an array.
     *
     * @return \Risan\OAuth1\Config\ConfigInterface
     * @throws \InvalidArgumentException
     */
    public static function createFromArray(array $config);
}
