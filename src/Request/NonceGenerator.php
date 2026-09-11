<?php

declare(strict_types=1);

namespace Risan\OAuth1\Request;

use InvalidArgumentException;

class NonceGenerator implements NonceGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate(int $length = 32): string
    {
        if ($length < 1) {
            throw new InvalidArgumentException('The nonce length must be greater than zero.');
        }

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
     */
    public function base64EncodedRandomBytes(int $length): string
    {
        if ($length < 1) {
            throw new InvalidArgumentException('The random byte length must be greater than zero.');
        }

        return base64_encode(random_bytes($length));
    }

    /**
     * Extract the alphanumeric characters from base64 encoded string.
     */
    public function extractAlphaNumericFromBase64EncodedString(string $string): string
    {
        return str_replace(['/', '+', '='], '', $string);
    }
}
