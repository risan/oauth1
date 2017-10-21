<?php

namespace Risan\OAuth1\Request;

class RequestConfig implements RequestConfigInterface
{
    /**
     * The request HTTP method.
     *
     * @var string
     */
    protected $method;

    /**
     * The request URI.
     *
     * @var string
     */
    protected $uri;

    /**
     * The request options.
     *
     * @var array
     */
    protected $options;

    /**
     * Create the new instance of RequestConfig class.
     *
     * @param string $method
     * @param string $uri
     * @param array  $options
     */
    public function __construct($method, $uri, $options = [])
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->options = $options;
    }

    /**
     * {@inheritDoc}
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * {@inheritDoc}
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        return $this->options;
    }
}
