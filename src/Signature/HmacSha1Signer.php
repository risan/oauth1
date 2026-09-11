<?php

declare(strict_types=1);

namespace Risan\OAuth1\Signature;

use Psr\Http\Message\UriInterface;

class HmacSha1Signer implements BaseStringSignerInterface, KeyBasedSignerInterface, SignerInterface
{
    use CanBuildBaseString,
        CanGetSigningKey;

    /**
     * {@inheritdoc}
     */
    public function getMethod(): string
    {
        return 'HMAC-SHA1';
    }

    /**
     * {@inheritdoc}
     */
    public function sign(UriInterface|string $uri, array $parameters = [], string $httpMethod = 'POST'): string
    {
        $baseString = $this->buildBaseString($uri, $parameters, $httpMethod);

        return base64_encode($this->hash($baseString));
    }

    /**
     * Hash the data with HMAC method.
     */
    public function hash(string $data): string
    {
        return hash_hmac('sha1', $data, $this->getKey(), true);
    }
}
