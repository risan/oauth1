<?php

declare(strict_types=1);

namespace Risan\OAuth1\Signature;

use Risan\OAuth1\Credentials\ClientCredentials;
use Risan\OAuth1\Credentials\ServerIssuedCredentials;

interface KeyBasedSignerInterface
{
    /**
     * Get the key for signing.
     */
    public function getKey(): string;

    /**
     * Set the ClientCredentials instance for signing.
     *
     * @return $this
     */
    public function setClientCredentials(ClientCredentials $clientCredentials): static;

    /**
     * Get the ClientCredentials instance for signing.
     */
    public function getClientCredentials(): ?ClientCredentials;

    /**
     * Set the ServerIssuedCredentials instance for signing.
     */
    public function setServerIssuedCredentials(ServerIssuedCredentials $serverIssuedCredentials): static;

    /**
     * Get the ServerIssuedCredentials instance for signing.
     */
    public function getServerIssuedCredentials(): ?ServerIssuedCredentials;
}
