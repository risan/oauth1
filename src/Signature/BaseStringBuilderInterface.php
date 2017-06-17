<?php

namespace Risan\OAuth1\Signature;

use Psr\Http\Message\UriInterface;

interface BaseStringBuilderInterface
{
    /**
     * Build the signature base string.
     *
     * @param string $httpMethod
     * @param \Psr\Http\Message\UriInterface|string $uri
     * @param array $parameters
     * @link https://tools.ietf.org/html/rfc5849#section-3.4.1
     * @return string
     */
    public function build($httpMethod, $uri, array $parameters = []);

    /**
     * Build the HTTP method component for base string.
     *
     * @param  string $httpMethod
     * @return string
     */
    public function buildMethodComponent($httpMethod);

    /**
     * Build the URI component for base string.
     *
     * @param  \Psr\Http\Message\UriInterface|string $uri
     * @link https://tools.ietf.org/html/rfc5849#section-3.4.1.2
     * @return string
     */
    public function buildUriComponent($uri);

    /**
     * Build the parameters component for base string.
     *
     * @param  array  $parameters
     * @link https://tools.ietf.org/html/rfc5849#section-3.4.1.3
     * @return string
     */
    public function buildParametersComponent(array $parameters);
}
