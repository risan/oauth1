<?php

namespace Risan\OAuth1\Tokens;

use Risan\OAuth1\Contracts\Tokens\TokenInterface;

abstract class Token implements TokenInterface
{
    /**
     * Token's key.
     *
     * @var string
     */
    protected $key;

    /**
     * Token's secret.
     *
     * @var string
     */
    protected $secret;

    /**
     * Create a new instance of Token.
     *
     * @param string $key
     * @param string $secret
     */
    public function __construct($key, $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * Get token's key.
     *
     * @return string
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * Get token's secret.
     *
     * @return string
     */
    public function secret()
    {
        return $this->secret;
    }
}
