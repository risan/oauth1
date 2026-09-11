<?php

declare(strict_types=1);

namespace Risan\OAuth1\Signature;

use Psr\Http\Message\UriInterface;

interface BaseStringSignerInterface
{
    /**
     * Build the signature base string.
     */
    public function buildBaseString(UriInterface|string $uri, array $parameters = [], string $httpMethod = 'POST'): string;

    /**
     * Get the BaseStringBuilder instance.
     */
    public function getBaseStringBuilder(): BaseStringBuilderInterface;
}
