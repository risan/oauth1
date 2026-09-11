<?php

declare(strict_types=1);

namespace Risan\OAuth1\Request;

class Request implements RequestInterface
{
    /**
     * The request HTTP method.
     */
    protected string $method;

    /**
     * The request URI.
     */
    protected string $uri;

    /**
     * The request options.
     */
    protected array $options;

    /**
     * Create a new instance of Request class.
     */
    public function __construct(string $method, string $uri, array $options = [])
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
