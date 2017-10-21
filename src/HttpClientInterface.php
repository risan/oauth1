<?php

namespace Risan\OAuth1;

use Risan\OAuth1\Request\RequestConfigInterface;

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
     * Send HTTP request.
     *
     * @param  \Risan\OAuth1\Request\RequestConfigInterface $requestConfig
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function send(RequestConfigInterface $requestConfig);

    /**
     * Create and send HTTP POST request.
     *
     * @param  string $uri
     * @param  array  $options
     * @return Psr\Http\Message\ResponseInterface
     */
    public function post($uri, array $options = []);
}
