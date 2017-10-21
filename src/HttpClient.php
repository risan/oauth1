<?php

namespace Risan\OAuth1;

use GuzzleHttp\Client as Guzzle;
use Risan\OAuth1\Request\RequestConfigInterface;

class HttpClient implements HttpClientInterface
{
    protected $guzzle;

    /**
     * Create an instance of HttpClient.
     */
    public function __construct(Guzzle $guzzle = null)
    {
        $this->guzzle = $guzzle === null ? new Guzzle : $guzzle;
    }

    /**
     * Get Guzzle client instance.
     *
     * @return \GuzzleHttp\Client
     */
    public function getGuzzle()
    {
        return $this->guzzle;
    }

    /**
     * {@inheritDoc}
     */
    public function request($method, $uri, array $options = [])
    {
        return $this->guzzle->request($method, $uri, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function send(RequestConfigInterface $requestConfig)
    {
        return $this->request(
            $requestConfig->getMethod(),
            $requestConfig->getUri(),
            $requestConfig->getOptions()
        );
    }

    /**
     * {@inheritDoc}
     */
    public function post($uri, array $options = [])
    {
        return $this->request('POST', $uri, $options);
    }
}
