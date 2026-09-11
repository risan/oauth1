<?php

declare(strict_types=1);

namespace Risan\OAuth1;

use GuzzleHttp\Client;
use GuzzleHttp\Client as Guzzle;
use Psr\Http\Message\ResponseInterface;
use Risan\OAuth1\Request\RequestInterface;

class HttpClient implements HttpClientInterface
{
    protected Guzzle $guzzle;

    /**
     * Create an instance of HttpClient.
     */
    public function __construct(?Guzzle $guzzle = null)
    {
        $this->guzzle = $guzzle === null ? new Guzzle : $guzzle;
    }

    /**
     * Get Guzzle client instance.
     */
    public function getGuzzle(): Guzzle
    {
        return $this->guzzle;
    }

    /**
     * {@inheritdoc}
     */
    public function request(string $method, string $uri, array $options = []): ResponseInterface
    {
        return $this->guzzle->request($method, $uri, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function send(RequestInterface $request): ResponseInterface
    {
        return $this->request(
            $request->getMethod(),
            $request->getUri(),
            $request->getOptions()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function post(string $uri, array $options = []): ResponseInterface
    {
        return $this->request('POST', $uri, $options);
    }
}
