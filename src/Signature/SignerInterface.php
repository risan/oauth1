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
     * Create a signature for given request parameters.
     *
     * @param  string $uri
     * @param  array  $parameters
     * @param  string $httpMethod
     * @return string
     */
    public function sign($uri, array $parameters = [], $httpMethod = 'POST');
}
