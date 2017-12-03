<?php

namespace Risan\OAuth1\Signature;

interface SignerInterface
{
    /**
     * Get signer method name.
     *
     * @return string
     */
    public function getMethod();

    /**
     * Check if the signer is key based.
     *
     * @return bool
     */
    public function isKeyBased();

    /**
     * Create a signature for given request parameters.
     *
     * @param string $uri
     * @param array  $parameters
     * @param string $httpMethod
     *
     * @return string
     */
    public function sign($uri, array $parameters = [], $httpMethod = 'POST');
}
