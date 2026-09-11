<?php

declare(strict_types=1);

namespace Risan\OAuth1\Signature;

use Psr\Http\Message\UriInterface;
use Risan\OAuth1\Request\UriParserInterface;

interface BaseStringBuilderInterface
{
    /**
     * Get the UriParserInterface instance.
     */
    public function getUriParser(): UriParserInterface;

    /**
     * Build the signature base string.
     *
     *
     * @see https://tools.ietf.org/html/rfc5849#section-3.4.1
     */
    public function build(string $httpMethod, UriInterface|string $uri, array $parameters = []): string;

    /**
     * Build the HTTP method component for base string.
     */
    public function buildMethodComponent(string $httpMethod): string;

    /**
     * Build the URI component for base string.
     *
     *
     * @see https://tools.ietf.org/html/rfc5849#section-3.4.1.2
     */
    public function buildUriComponent(UriInterface|string $uri): string;

    /**
     * Build the parameters component for base string.
     *
     *
     * @see https://tools.ietf.org/html/rfc5849#section-3.4.1.3
     */
    public function buildParametersComponent(array $parameters): string;
}
