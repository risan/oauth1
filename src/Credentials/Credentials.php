<?php

namespace OAuth1Client\Credentials;

use OAuth1Client\Contracts\Credentials\CredentialsInterface;

abstract class Credentials implements CredentialsInterface {
    /**
     * Credentials identifier.
     *
     * @var string
     */
    protected $identifier;

    /**
     * Credentials secret.
     *
     * @var string
     */
    protected $secret;

    /**
     * Create a new instance of Credentials.
     *
     * @param string $identifier
     * @param string $secret
     */
    public function __construct($identifier, $secret)
    {
        $this->identifier = $identifier;
        $this->secret = $secret;
    }

    /**
     * Get credentials identifier.
     *
     * @return string
     */
    public function identifier()
    {
        return $this->identifier;
    }

    /**
     * Get credentials secret.
     *
     * @return string
     */
    public function secret()
    {
        return $this->secret;
    }

    /**
     * Case Credentials instance to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->identifier();
    }
}
