<?php

namespace Risan\OAuth1;

use GuzzleHttp\Client as Guzzle;
use Risan\OAuth1\Request\RequestInterface;

class HttpClient implements HttpClientInterface
{
    protected $guzzle;

    /**
     * Create an instance of HttpClient.
     */
    public function __construct(Guzzle $guzzle = null)
    {
        $this->guzzle = null === $guzzle ? new Guzzle() : $guzzle;
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
     * {@inheritdoc}
     */
    public function request($method, $uri, array $options = [])
    {
        return $this->guzzle->request($method, $uri, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function send(RequestInterface $request)
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
    public function post($uri, array $options = [])
    {
        return $this->request('POST', $uri, $options);
    }
}
