<?php

declare(strict_types=1);

namespace Risan\OAuth1\Signature;

use Risan\OAuth1\Credentials\ClientCredentials;
use Risan\OAuth1\Credentials\ServerIssuedCredentials;

trait CanGetSigningKey
{
    /**
     * The ClientCredentials instance.
     */
    protected ?ClientCredentials $clientCredentials = null;

    /**
     * The ServerIssuedCredentials instance.
     */
    protected ?ServerIssuedCredentials $serverIssuedCredentials = null;

    /**
     * Check if the signer is key based.
     */
    public function isKeyBased(): bool
    {
        return true;
    }

    /**
     * Get the key for signing.
     */
    public function getKey(): string
    {
        $key = '';

        if ($this->clientCredentials instanceof ClientCredentials) {
            $key .= rawurlencode($this->clientCredentials->getSecret());
        }

        // Keep the ampersand even if both keys are empty.
        $key .= '&';

        if ($this->serverIssuedCredentials instanceof ServerIssuedCredentials) {
            $key .= rawurlencode($this->serverIssuedCredentials->getSecret());
        }

        return $key;
    }

    /**
     * Set the ClientCredentials instance for signing.
     *
     * @return $this
     */
    public function setClientCredentials(ClientCredentials $clientCredentials): static
    {
        $this->clientCredentials = $clientCredentials;

        return $this;
    }

    /**
     * Get the ClientCredentials instance for signing.
     */
    public function getClientCredentials(): ?ClientCredentials
    {
        return $this->clientCredentials;
    }

    /**
     * Set the ServerIssuedCredentials instance for signing.
     *
     * @return $this
     */
    public function setServerIssuedCredentials(ServerIssuedCredentials $serverIssuedCredentials): static
    {
        $this->serverIssuedCredentials = $serverIssuedCredentials;

        return $this;
    }

    /**
     * Get the ServerIssuedCredentials instance for signing.
     */
    public function getServerIssuedCredentials(): ?ServerIssuedCredentials
    {
        return $this->serverIssuedCredentials;
    }
}
