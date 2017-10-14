<?php

namespace Risan\OAuth1;

interface HttpClientInterface
{
    /**
     * Create and send HTTP request.
     *
     * @param  string $method
     * @param  string $uri
     * @param  array  $options
     * @return Psr\Http\Message\ResponseInterface
     */
    public function request($method, $uri, array $options = []);

    /**
     * Create and send HTTP POST request.
     *
     * @param  string $uri
     * @param  array  $options
     * @return Psr\Http\Message\ResponseInterface
     */
    public function post($uri, array $options = []);
}
