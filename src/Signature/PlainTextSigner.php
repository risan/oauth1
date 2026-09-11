<?php

declare(strict_types=1);

namespace Risan\OAuth1\Signature;

use Psr\Http\Message\UriInterface;

class PlainTextSigner implements KeyBasedSignerInterface, SignerInterface
{
    use CanGetSigningKey;

    /**
     * {@inheritdoc}
     */
    public function getMethod(): string
    {
        return 'PLAINTEXT';
    }

    /**
     * {@inheritdoc}
     */
    public function sign(UriInterface|string $uri, array $parameters = [], string $httpMethod = 'POST'): string
    {
        return $this->getKey();
    }
}
