<?php

namespace Risan\OAuth1\Contracts\Signers;

interface SignerInterface
{
    /**
     * Get signer's consumer secret.
     *
     * @return string
     */
    public function consumerSecret();

    /**
     * Get signer's token secret.
     *
     * @return string|null
     */
    public function tokenSecret();

    /**
     * Set signer's token secret.
     *
     * @param string $secret
     *
     * @return \OAuth1\Contracts\Signers\SignerInterface
     */
    public function setTokenSecret($secret);

    /**
     * Get signer's method name.
     *
     * @return string
     */
    public function method();

    /**
     * Get signer's key.
     *
     * @return string
     */
    public function key();

    /**
     * Sign request for the client.
     *
     * @param string $uri
     * @param array  $parameters
     * @param string $httpVerb
     *
     * @return string
     */
    public function sign($uri, array $parameters = [], $httpVerb = 'POST');
}
