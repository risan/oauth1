<?php

namespace Risan\OAuth1;

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
     * Check if callback URI is set.
     *
     * @return boolean
     */
    public function hasCallbackUri();

    /**
     * Get the callback URI.
     *
     * @return string|null
     */
    public function getCallbackUri();

    /**
     * Get the URL for obtaining temporary credentials. Also known as request
     * token url.
     *
     * @return string
     */
    public function getTemporaryCredentialsUrl();

    /**
     * Create an instance of ConfigureInterface from array.
     *
     * @return \Risan\OAuth1\ConfigInterface
     * @throws \InvalidArgumentException
     */
    public static function createFromArray(array $config);
}
