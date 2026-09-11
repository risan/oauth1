<?php

declare(strict_types=1);

namespace Risan\OAuth1\Signature;

use Psr\Http\Message\UriInterface;

interface SignerInterface
{
    /**
     * Get signer method name.
     */
    public function getMethod(): string;

    /**
     * Check if the signer is key based.
     */
    public function isKeyBased(): bool;

    /**
     * Create a signature for given request parameters.
     *
     * @param  string  $uri
     */
    public function sign(UriInterface|string $uri, array $parameters = [], string $httpMethod = 'POST'): string;
}
