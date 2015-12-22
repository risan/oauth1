<?php

namespace OAuth1Client\Signatures;

use OAuth1Client\Contracts\Signatures\SignatureInterface;
use OAuth1Client\Contracts\Credentials\CredentialsInterface;
use OAuth1Client\Contracts\Credentials\ClientCredentialsInterface;

abstract class Signature implements SignatureInterface {
    /**
     * Signature's client credentials.
     *
     * @var OAuth1Client\Contracts\Credentials\ClientCredentialsInterface
     */
    protected $clientCredentials;

    /**
     * Signature's credentials.
     *
     * @var OAuth1Client\Contracts\Credentials\CredentialsInterface
     */
    protected $credentials;

    /**
     * Create a new instance of Signature.
     *
     * @param OAuth1Client\Contracts\Credentials\ClientCredentialsInterface $clientCredentials
     */
    public function __construct(ClientCredentialsInterface $clientCredentials)
    {
        $this->clientCredentials = $clientCredentials;
    }

    /**
     * Get client credentials.
     *
     * @return OAuth1Client\Contracts\Credentials\ClientCredentialsInterface
     */
    public function clientCredentials()
    {
        return $this->clientCredentials;
    }

    /**
     * Get signature's credentials.
     *
     * @return OAuth1Client\Contracts\Credentials\CredentialsInterface
     */
    public function credentials()
    {
        return $this->credentials;
    }

    /**
     * Set signature's credentials.
     *
     * @param OAuth1Client\Contracts\Credentials\CredentialsInterface $credentials
     * @return OAuth1Client\Contracts\Signatures\SignatureInterface
     */
    public function setCredentials(CredentialsInterface $credentials)
    {
        $this->credentials = $credentials;

        return $this;
    }

    /**
     * Get signature's key.
     *
     * @return string
     */
    public function key()
    {
        $key = rawurlencode($this->clientCredentials()->secret()) . '&';

        if ($this->credentials() instanceof CredentialsInterface) {
            $key .= rawurlencode($this->credentials()->secret());
        }

        return $key;
    }
}
