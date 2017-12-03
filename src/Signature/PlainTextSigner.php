<?php

namespace Risan\OAuth1\Signature;

class PlainTextSigner implements SignerInterface, KeyBasedSignerInterface
{
    use CanGetSigningKey;

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return 'PLAINTEXT';
    }

    /**
     * {@inheritdoc}
     */
    public function sign($uri, array $parameters = [], $httpMethod = 'POST')
    {
        return $this->getKey();
    }
}
