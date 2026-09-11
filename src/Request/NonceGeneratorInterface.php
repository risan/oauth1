<?php

declare(strict_types=1);

namespace Risan\OAuth1\Request;

interface NonceGeneratorInterface
{
    /**
     * Generate a random string for nonce.
     */
    public function generate(int $length = 32): string;
}
