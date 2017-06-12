<?php

namespace Risan\OAuth1\Signers;

use Risan\OAuth1\Contracts\Signers\SignerInterface;

abstract class Signer implements SignerInterface
{
    /**
     * Signer's consumer secret.
     *
     * @var string
     */
    protected $consumerSecret;

    /**
     * Signer's token secret.
     *
     * @var string|null
     */
    protected $tokenSecret;

    /**
     * Create a new instance of Signer.
     *
     * @param string $consumerSecret
     */
    public function __construct($consumerSecret)
    {
        $this->consumerSecret = $consumerSecret;
    }

    /**
     * Get signer's consumer secret.
     *
     * @return string
     */
    public function consumerSecret()
    {
        return $this->consumerSecret;
    }

    /**
     * Get signer's token secret.
     *
     * @return string|null
     */
    public function tokenSecret()
    {
        return $this->tokenSecret;
    }

    /**
     * Set signer's token secret.
     *
     * @param string $secret
     *
     * @return \OAuth1\Contracts\Signers\SignerInterface
     */
    public function setTokenSecret($secret)
    {
        $this->tokenSecret = $secret;

        return $this;
    }

    /**
     * Get signer's key.
     *
     * @return string
     */
    public function key()
    {
        $key = rawurlencode($this->consumerSecret()).'&';

        if (!is_null($this->tokenSecret())) {
            $key .= rawurlencode($this->tokenSecret());
        }

        return $key;
    }
}
