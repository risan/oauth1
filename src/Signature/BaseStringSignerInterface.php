<?php

namespace Risan\OAuth1\Signature;

interface BaseStringSignerInterface
{
    /**
     * Build the signature base string.
     *
     * @param \Psr\Http\Message\UriInterface|string $uri
     * @param array                                 $parameters
     * @param string                                $httpMethod
     *
     * @return string
     */
    public function buildBaseString($uri, array $parameters = [], $httpMethod = 'POST');

    /**
     * Get the BaseStringBuilder instance.
     *
     * @return \Risan\OAuth1\Signature\BaseStringBuilderInterface
     */
    public function getBaseStringBuilder();
}
