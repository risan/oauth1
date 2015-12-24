<?php

namespace OAuth1\Contracts\Signers;

interface HmacSha1SignerInterface extends SignerInterface {
    /**
     * Construct HMAC-SHA1 signature base string.
     *
     * @param  string $uri
     * @param  array  $parameters
     * @param  string $httpVerb
     * @return string
     */
    public function baseString($uri, array $parameters = [], $httpVerb = 'POST');

    /**
     * Hash the given data with signer's key.
     *
     * @param  string $data
     * @return string
     */
    public function hash($data);
}
