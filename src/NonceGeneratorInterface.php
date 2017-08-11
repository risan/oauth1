<?php

namespace Risan\OAuth1;

interface NonceGeneratorInterface
{
    /**
     * Generate a random string for nonce.
     *
     * @param  integer $length
     * @return string
     */
    public function generate($length = 32);
}
