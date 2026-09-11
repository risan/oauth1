<?php

declare(strict_types=1);

namespace Risan\OAuth1;

use Psr\Http\Message\ResponseInterface;
use Risan\OAuth1\Request\RequestInterface;

interface HttpClientInterface
{
    /**
     * Create and send HTTP request.
     */
    public function request(string $method, string $uri, array $options = []): ResponseInterface;

    /**
     * Send HTTP request.
     */
    public function send(RequestInterface $request): ResponseInterface;

    /**
     * Create and send HTTP POST request.
     */
    public function post(string $uri, array $options = []): ResponseInterface;
}
