<?php

namespace OAuth1Client\Signatures;

use OAuth1Client\Contracts\Credentials\CredentialsInterface;
use OAuth1Client\Contracts\Signatures\HMACSHA1SignatureInterface;
use OAuth1Client\Contracts\Credentials\ClientCredentialsInterface;

class HMACSHA1Signature extends Signature implements HMACSHA1SignatureInterface {
    /**
     * Get signature's method name.
     *
     * @return string
     */
    public function method()
    {
        return 'HMAC-SHA1';
    }

    /**
     * Sign request for the client.
     *
     * @param  string $uri
     * @param  array  $parameters
     * @param  string $httpVerb
     * @return string
     */
    public function sign($uri, array $parameters = [], $httpVerb = 'POST')
    {
        $baseString = $this->baseString($uri, $parameters, $httpVerb);

        return base64_encode($this->hash($baseString));
    }

    /**
     * Construct HMAC-SHA1 signature base string.
     *
     * @param  string $uri
     * @param  array  $parameters
     * @param  string $httpVerb
     * @return string
     */
    public function baseString($uri, array $parameters = [], $httpVerb = 'POST')
    {
        ksort($parameters);

        $parameters = http_build_query($parameters, '', '&', PHP_QUERY_RFC3986);

        return sprintf("%s&%s&%s", $httpVerb, rawurlencode($uri), rawurlencode($parameters));
    }

    /**
     * Hash the given data with signature's key.
     *
     * @param  string $data
     * @return string
     */
    public function hash($data)
    {
        return hash_hmac('sha1', $data, $this->key(), true);
    }
}
