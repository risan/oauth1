<?php

namespace OAuth1\Contracts\Tokens;

interface TokenInterface {
    /**
     * Get token's key.
     *
     * @return string
     */
    public function key();

    /**
     * Get token's secret.
     *
     * @return string
     */
    public function secret();
}
