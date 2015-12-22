<?php

namespace OAuth1Client\Contracts\Signatures;

use OAuth1Client\Contracts\Credentials\CredentialsInterface;

interface SignatureInterface {
    /**
     * Get signature's method name.
     *
     * @return string
     */
    public function method();

    /**
     * Get client credentials.
     *
     * @return OAuth1Client\Contracts\Credentials\ClientCredentialsInterface
     */
    public function clientCredentials();

    /**
     * Get signature's credentials.
     *
     * @return OAuth1Client\Contracts\Credentials\CredentialsInterface
     */
    public function credentials();

    /**
     * Set signature's credentials.
     *
     * @param OAuth1Client\Contracts\Credentials\CredentialsInterface $credentials
     */
    public function setCredentials(CredentialsInterface $credentials);

    /**
     * Sign request for the client.
     *
     * @param  string $uri
     * @param  array  $parameters
     * @param  string $httpVerb
     * @return string
     */
    public function sign($uri, array $parameters = [], $httpVerb = 'POST');

    /**
     * Get signature's key.
     *
     * @return string
     */
    public function key();
}
