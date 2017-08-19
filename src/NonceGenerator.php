<?php

namespace Risan\OAuth1;

class NonceGenerator implements NonceGeneratorInterface
{
    /**
     * {@inheritDoc}
     */
    public function generate($length = 32)
    {
        $nonce = '';

        while (($currentNonceLength = strlen($nonce)) < $length) {
            $size = $length - $currentNonceLength;
            $randomString = $this->base64EncodedRandomBytes($size);
            $nonce .= substr($this->extractAlphaNumericFromBase64EncodedString($randomString), 0, $size);
        }

        return $nonce;
    }

    /**
     * Get cryptographically secure base64 encoded random bytes.
     *
     * @param  int $length
     * @return string
     */
    public function base64EncodedRandomBytes($length)
    {
        return base64_encode(random_bytes($length));
    }

    /**
     * Extract the alphanumeric characters from base64 encoded string.
     *
     * @param  string $string
     * @return string
     */
    public function extractAlphaNumericFromBase64EncodedString($string)
    {
        return str_replace(['/', '+', '='], '', $string);
    }
}
