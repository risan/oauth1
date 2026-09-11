<?php

declare(strict_types=1);

namespace Risan\OAuth1\Request;

interface RequestInterface
{
    /**
     * Get the request HTTP method.
     */
    public function getMethod(): string;

    /**
     * Get the request URI.
     */
    public function getUri(): string;

    /**
     * Get the request options.
     */
    public function getOptions(): array;
}
