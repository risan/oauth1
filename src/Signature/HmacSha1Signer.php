<?php

namespace Risan\OAuth1\Signature;

use Risan\OAuth1\Credentials\ClientCredentials;

class HmacSha1Signer implements SignerInterface, BaseStringSignerInterface, KeyBasedSignerInterface
{
    use CanBuildBaseString,
        CanGetSigningKey;

    /**
     * {@inheritDoc}
     */
    public function method()
    {
        return 'HMAC-SHA1';
    }

    /**
     * {@inheritDoc}
     */
    public function sign($uri, array $parameters = [], $httpMethod = 'POST')
    {
        $baseString = $this->buildBaseString($uri, $parameters, $httpMethod);

        return base64_encode($this->hash($baseString));
    }

    /**
     * Hash the data with HMAC method.
     *
     * @param  string $data
     * @return string
     */
    public function hash($data)
    {
        return hash_hmac('sha1', $data, $this->getKey(), true);
    }
}
